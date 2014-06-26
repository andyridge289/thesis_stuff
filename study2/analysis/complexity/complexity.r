library(psych)
library(ctv)
library(ggplot2)
library(boot)

# install.views("Psychometrics")
# if(!exists("tukey", mode="function")) source("games_howell.R")

# summary(data)
# describe(data)

# anova(lm(num ~ condition, all.data))
# tukey(data, data$condition, "Games-Howell")

# things needs to be different to all of the others because it's only applicable for conditions 4 and 5
# data <- read.csv("things_12345_r.csv")
# t.test(data$c4, data$c5)

setwd("/Applications/XAMPP/htdocs/htdocs/thesis_stuff/study2/analysis/complexity")

data3 <- read.csv("complexity_3.csv")
data5 <- read.csv("complexity_5.csv")
data124 <- read.csv("complexity_124.csv")
data135 <- read.csv("complexity_135.csv")
colours3 <- c("thistle", "wheat", "tomato")
colours5 <- c("thistle", "springgreen1", "springgreen4", "steelblue1", "steelblue4")
# ggplot(data, aes(x=Condition, y=Level, color=Representation)) + scale_color_hue(l=50) + geom_point(shape=1) + geom_smooth(method=lm, se=FALSE) + scale_x_continuous(breaks=c(seq(1,3,by=1)))

shapiro.test(data5$total)
qqnorm(data5$total, main="Normality for complexity", ylab="Number of design elements")
qqline(data5$total, col=2, probs=c(0.1,0.9))

p <- ggplot(data5, aes(x=condition, y=total))
p <- ggplot(data3, aes(x=condition, y=total, color=reopresentation)) 
p <- ggplot(data124, aes(x=condition, y=total))
p <- ggplot(data135, aes(x=condition, y=total))
p + stat_summary(fun.data="mean_cl_boot") + geom_smooth(method=lm, se=FALSE)

fred <- function(x,i) mean(x[i,3])
b <- boot(data124[data124$condition==1,], fred, R=1000,)
geoff <- boot.ci(b)
summary(geoff)
print(geoff$normal)

hist(data124$total)
a <- lm(total~condition, data124)
summary(a)
plot(data124$condition, data124$total)

#cor.test(data3$Condition, data3$Level, method="pearson")
cor.test(data3$condition, data3$total, method="spearman")
#cor.test(data124$Condition, data124$Level, method="pearson")
cor.test(data124$condition, data124$total, method="spearman")
#cor.test(data135$Condition, data135$Level, method="pearson")
cor.test(data135$condition, data135$total, method="spearman")
#boxplot(Level~Condition, data=data5, col=colours5, ylab="Abstractness", xlab="Condition", main="Design Abstractness")

# data <- read.csv("all_12345_r.csv")
# ggplot(data, aes(x=Condition, y=Total, color=Representation)) + scale_color_hue(l=50) + geom_point(shape=1) + geom_smooth(method=lm, se=FALSE) + scale_x_continuous(breaks=c(seq(1,3,by=1)))

#data <- read.csv("custom_12345_r.csv")
#ggplot(data, aes(x=Condition, y=Total, color=Representation)) + scale_color_hue(l=50) + geom_point(shape=1) + geom_smooth(method=lm, se=FALSE) + scale_x_continuous(breaks=c(seq(1,3,by=1)))

#data <- read.csv("new_12345_r.csv")
#ggplot(data, aes(x=Condition, y=Total, color=Representation)) + scale_color_hue(l=50) + geom_point(shape=1) + geom_smooth(method=lm, se=FALSE) + scale_x_continuous(breaks=c(seq(1,3,by=1)))

#data <- read.csv("completeness.csv")
#ggplot(data, aes(x=condition, y=groups_1, color=representation)) + scale_color_hue(l=50) + geom_point(shape=1) + geom_smooth(method=lm, se=FALSE) + scale_y_continuous(name="Groups with > 1 element") + scale_x_continuous(name="DS Level", breaks=c(seq(1,3,by=1)))
#ggplot(data, aes(x=condition, y=groups_10, color=representation)) + scale_color_hue(l=50) + geom_point(shape=1) + geom_smooth(method=lm, se=FALSE) + scale_y_continuous(name="Groups with > 10 elements") + scale_x_continuous(name="DS Level", breaks=c(seq(1,3,by=1)))
#ggplot(data, aes(x=condition, y=groups_20, color=representation)) + scale_color_hue(l=50) + geom_point(shape=1) + geom_smooth(method=lm, se=FALSE) + scale_y_continuous(name="Groups with > 20 elements") + scale_x_continuous(name="DS Level", breaks=c(seq(1,3,by=1)))

#ggplot(data, aes(x=condition, y=categories_1, color=representation)) + scale_color_hue(l=50) + geom_point(shape=1) + geom_smooth(method=lm, se=FALSE) + scale_y_continuous(name="Categories with > 1 element") + scale_x_continuous(name="DS Level", breaks=c(seq(1,3,by=1)))
#ggplot(data, aes(x=condition, y=categories_10, color=representation)) + scale_color_hue(l=50) + geom_point(shape=1) + geom_smooth(method=lm, se=FALSE) + scale_y_continuous(name="Categories with > 10 elements") + scale_x_continuous(name="DS Level", breaks=c(seq(1,3,by=1)))
#ggplot(data, aes(x=condition, y=categories_20, color=representation)) + scale_color_hue(l=50) + geom_point(shape=1) + geom_smooth(method=lm, se=FALSE) + scale_y_continuous(name="Categories with > 20 elements") + scale_x_continuous(name="DS Level", breaks=c(seq(1,3,by=1)))
# qplot(data$condition, data$num, data)
