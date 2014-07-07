library(psych)
library(ctv)
library(ggplot2)

setwd("/Applications/XAMPP/htdocs/htdocs/thesis_stuff/study2/analysis/workload")

data5 <- read.csv("workload_5.csv")
data3 <- read.csv("workload_3.csv")
data124 <- read.csv("workload_124.csv")
data135 <- read.csv("workload_135.csv")
colours3 <- c("thistle", "wheat", "tomato")
colours5 <- c("thistle", "springgreen1", "springgreen4", "steelblue1", "steelblue4")

#ggplot(data, aes(x=Condition, y=Level, color=Representation) + scale_color_hue(l=50) + geom_point(shape=1) + geom_smooth(method=lm, se=FALSE) + scale_x_continuous(breaks=c(seq(1,3,by=1)))

ggplot(data5, aes(x=factor(condition), y=workloadWeighted_frustration)) + stat_summary(fun.data="mean_cl_boot", geom="crossbar", width=0.3, fill=colours5) + scale_x_discrete(name="Condition",breaks=c(seq(1,5,by=1))) + scale_y_continuous(name="Weighted frustration")

#fred <- function(x,i) mean(x[i,3])

#a <- boot.ci(boot(data5[data5$condition==1,], fred, R=1000,))
#b <- boot.ci(boot(data5[data5$condition==2,], fred, R=1000,))
#c <- boot.ci(boot(data5[data5$condition==3,], fred, R=1000,))
#d <- boot.ci(boot(data5[data5$condition==4,], fred, R=1000,))
#e <- boot.ci(boot(data5[data5$condition==5,], fred, R=1000,))

#print(a$normal)
#print(b$normal)
#print(c$normal)
#print(d$normal)
#print(e$normal)

one <- data5[ which(data5$condition == 1), ]
two <- data5[ which(data5$condition == 2), ]
three <- data5[ which(data5$condition == 3), ]
four <- data5[ which(data5$condition == 4), ]
five <- data5[ which(data5$condition == 5), ]

mean(two$workloadWeighted_frustration)
mean(four$workloadWeighted_frustration)

cor.test(data3$condition, data3$workloadWeighted_frustration, method="spearman")
cor.test(data124$condition, data124$workloadWeighted_frustration, method="spearman")
cor.test(data135$condition, data135$workloadWeighted_frustration, method="spearman")

shapiro.test(data5$workloadWeighted_physical)
qqnorm(data5$workloadWeighted_frustration, ylab="TLX Score")
qqline(data5$workloadWeighted_frustration, col=2, probs=c(0.1,0.9))
