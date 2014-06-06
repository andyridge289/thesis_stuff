library(psych)
library(ctv)
#install.packages('ggplot2', dep = TRUE)
library(ggplot2)

#install.views("Psychometrics")
#if(!exists("tukey", mode="function")) source("games_howell.R")

#summary(data)
describe(data)

#anova(lm(num ~ condition, all.data))
#tukey(data, data$condition, "Games-Howell")

# things needs to be different to all of the others because it's only applicable for conditions 4 and 5
data <- read.csv("things_12345_r.csv")
t.test(data$c4, data$c5)

setwd("/Applications/XAMPP/htdocs/htdocs/thesis_stuff/study2/analysis")

data <- read.csv("all_12345_r.csv")
ggplot(data, aes(x=Condition, y=Total, color=Representation)) + scale_color_hue(l=50) + geom_point(shape=1) + geom_smooth(method=lm, se=FALSE) + scale_x_continuous(breaks=c(seq(1,3,by=1)))

data <- read.csv("custom_12345_r.csv")
ggplot(data, aes(x=Condition, y=Total, color=Representation)) + scale_color_hue(l=50) + geom_point(shape=1) + geom_smooth(method=lm, se=FALSE) + scale_x_continuous(breaks=c(seq(1,3,by=1)))

data <- read.csv("new_12345_r.csv")
ggplot(data, aes(x=Condition, y=Total, color=Representation)) + scale_color_hue(l=50) + geom_point(shape=1) + geom_smooth(method=lm, se=FALSE) + scale_x_continuous(breaks=c(seq(1,3,by=1)))


#qplot(data$condition, data$num, data)
