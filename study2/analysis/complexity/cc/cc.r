library(psych)
library(ctv)
library(ggplot2)
library(boot)

setwd("/Applications/XAMPP/htdocs/htdocs/thesis_stuff/study2/analysis/complexity/cc")

data3 <- read.csv("cc_3.csv")
data5 <- read.csv("cc_5.csv")
data124 <- read.csv("cc_124.csv")
data135 <- read.csv("cc_135.csv")
colours3 <- c("thistle", "wheat", "tomato")
colours5 <- c("thistle", "springgreen1", "springgreen4", "steelblue1", "steelblue4")

shapiro.test(data5$cperd)
qqnorm(data5$cperd, ylab="Number of causal links")
qqline(data5$cperd, col=2, probs=c(0.1,0.9))

ggplot(data5, aes(x=factor(condition), y=cperd)) + stat_summary(fun.data="mean_cl_boot", geom="crossbar", width=0.3, fill=colours5) + scale_x_discrete(name="Condition",breaks=c(seq(1,5,by=1))) + scale_y_continuous(name="Number of causal links")

fred <- function(x,i) mean(x[i])

a <- boot.ci(boot(data5[data5$condition==1,5], fred, R=1000,))
b <- boot.ci(boot(data5[data5$condition==2,5], fred, R=1000,))
c <- boot.ci(boot(data5[data5$condition==3,5], fred, R=1000,))
d <- boot.ci(boot(data5[data5$condition==4,5], fred, R=1000,))
e <- boot.ci(boot(data5[data5$condition==5,5], fred, R=1000,))

print(a$normal)
print(b$normal)
print(c$normal)
print(d$normal)
print(e$normal)

cor.test(data3$condition, data3$cperd, method="spearman")
cor.test(data124$condition, data124$cperd, method="spearman")
cor.test(data135$condition, data135$cperd, method="spearman")