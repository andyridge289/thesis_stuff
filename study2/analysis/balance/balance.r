library(psych)
library(ctv)
library(ggplot2)
library(boot)
library(reshape)


#Molten$condition = as.factor(Molten$condition)
#Molten$participant = as.factor(Molten$participant)

#ggplot(data=Molten, aes(x=variable, y=value, colour=condition)) + geom_line() + geom_point()

#tree <- Molten[which(Molten$condition < 3 | Molten$condition == 4),]
#tree$condition[tree$condition == 1] <- 3
#tree$condition <- as.factor(tree$condition)
ggplot(data=Molten, aes(x=variable, y=value, colour=condition)) + geom_line() + geom_point() + scale_colour_manual(values=c("#FFC7C1","#FF8E5E","#E4321B","#904598","#520D44"))

#list <- Molten[which(Molten$condition  == 1 | Molten$condition == 3 | Molten$condition == 5),]


setwd("/Applications/XAMPP/htdocs/htdocs/thesis_stuff/study2/analysis/balance")

data <- as.data.frame(read.csv("threshold_correlations.csv"))
#data124 <- subset(data, c == 1 | c == 2 | c == 4)
#data135 <- subset(data, c == 1 | c == 3 | c == 5)

#names <- names(data124)
#names <- names[-1]
#for(i in names) {
  #p[i] <- cor.test(data124$c, data124[,i], method="spearman")$estimate
  #q[i] <- cor.test(data135$c, data135[,i], method="spearman")$estimate
#}

#p <- as.data.frame(p)
#q <- as.data.frame(q)
#d <- t(d)
#d <- as.data.frame(d)
#d<-rename(d, c("1"="tree", "2"="list"))

data <- as.data.frame(read.csv("collated_correlations.csv"))
raw <- as.data.frame(read.csv("raw_correlations.csv"))
#keep <- c("threshold","Tree","List")
#data <- data[keep]

cor.test(raw$threshold, raw[,"tree"], method="spearman")
cor.test(raw$threshold, raw[,"list"], method="spearman")

cor.test(data$Threshold, data[,"Tree"], method="spearman")
cor.test(data$Threshold, data[,"List"], method="spearman")

meltedraw <- melt(raw, id=c("threshold"))
meltedraw <- rename(meltedraw, c("variable"="Representation"))
p <- ggplot(meltedraw, aes(x=threshold, y=value, colour=Representation))
p + geom_line() + scale_x_discrete(name="Threshold",breaks=c(seq(1,10,by=1))) + scale_y_continuous(name="Spearman's rho", breaks=c(seq(0,1,b=0.1)))

melted <- melt(data, id=c("Threshold"))
melted <- rename(melted, c("variable"="Representation"))



p <- ggplot(melted, aes(x=Threshold, y=value, colour=Representation))
p + geom_line() + scale_x_discrete(name="Threshold",breaks=c(seq(1,10,by=1))) + scale_y_continuous(name="Spearman's rho", breaks=c(seq(0,1,b=0.1)))

#ggplot(data5, aes(x=factor(condition), y=total)) + scale_x_discrete(name="Condition",breaks=c(seq(1,10,by=1))) + scale_y_continuous(name="Balance score", breaks=c(seq(0,1,b=0.1)))

# first compare against a flat distribution
#out <- list()
#for(i in c(1:nrow (data))) {
  #out[i] <- as.double((chisq.test(data[i,c(-1,-2)], rescale.p=FALSE))[3])
#}

#data$x <- 1:40
#for(i  in c(1:nrow(data))) {
#  data$x[i] <- as.double(out[i][1])
#}

#write.csv(data, file = "out.csv")

# Now do the mean ones
#data2 <- as.data.frame(t(t(read.csv("balance_raw.csv"))))
#means <- colMeans(data2)
#means <- means[-1]
#means <- means[-1]
#out <- list()
#for(i in c(1:nrow (data))) {
#  out[i] <- as.double((chisq.test(data2[i,c(-1,-2)], means, rescale.p=TRUE))[3])
#}

#data2$x <- 1:40
#for(i  in c(1:nrow(data2))) {
#  data2$x[i] <- as.double(out[i][1])
#}

#write.csv(data2, file = "out_mean.csv")

# Collated

#collated <- as.data.frame(t(t(read.csv("balance_collated.csv"))))
#out <- list()
#for(i in c(1:nrow (collated))) {
#  out[i] <- as.double((chisq.test(collated[i,c(-1,-2)], rescale.p=TRUE))[3])
#}

#collated$x <- 1:40
#for(i  in c(1:nrow(collated))) {
#  collated$x[i] <- as.double(out[i][1])
#}

#write.csv(collated, file = "collated_out.csv")

# And now the collated means
#collated2 <- as.data.frame(t(t(read.csv("balance_collated.csv"))))
#collatedMeans <- colMeans(collated2)
#collatedMeans <- collatedMeans[-1]
#collatedMeans <- collatedMeans[-1]
#out <- list()
#for(i in c(1:nrow (collated2))) {
#  out[i] <- as.double((chisq.test(collated2[i,c(-1,-2)], collatedMeans, rescale.p=TRUE))[3])
#}

#collated2$x <- 1:40
#for(i  in c(1:nrow(collated2))) {
#  collated2$x[i] <- as.double(out[i][1])
#}

#write.csv(collated2, file = "collated_mean_out.csv")

#plot(x=collated)

# correlation balanace
#data3 <- read.csv("b10_3.csv")
#data5 <- read.csv("b10_5.csv")
#data124 <- read.csv("b10_124.csv")
#data135 <- read.csv("b10_135.csv")

# Spearman again because condition is still ordinal
#cor.test(data3$condition, data3$total, method="spearman")
#cor.test(data124$condition, data124$total, method="spearman")
#cor.test(data135$condition, data135$total, method="spearman")

#shapiro.test(data5$total)

#qqnorm(data5$total, ylab="Number of design elements")
#qqline(data5$total, col=2, probs=c(0.1,0.9))

#colours5 <- c("thistle", "springgreen1", "springgreen4", "steelblue1", "steelblue4")


#fred <- function(x,i) mean(x[i,7])

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

