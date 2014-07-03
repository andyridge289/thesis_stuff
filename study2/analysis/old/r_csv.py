import os
import string

filenames = ["custom_12345.csv", "new_12345.csv", "things_12345.csv"];

for filename in filenames:

	lines = [];
	f = open(filename)
	ones = [];

	for line in f:

		data = string.split(line, ",")

		if "1" in data[0]:
			ones.append(line)
			line = line.rstrip()
			line += ",tree\n"
			lines.append(line)
		elif "2" in data[0]:
			line = line.rstrip()
			line += ",tree\n"
			lines.append(line)
		elif "3" in data[0]:
			line = "2," + data[1].rstrip() + ",list\n"
			lines.append(line)
		elif "4" in data[0]:
			line = "3," + data[1].rstrip()  + ",tree\n"
			lines.append(line)
		elif "5" in data[0]:
			line = "3," + data[1].rstrip() + ",list\n"
			lines.append(line)

	for line in ones:
		line = line.rstrip()
		line += ",list\n"
		lines.append(line)

	# And now write the file	
	name_pref = filename.split(".")
	out = open(name_pref[0] + "_r.csv", "w+")
	out.write("Condition,Total,Representation\n")
	for line in lines:
	 	out.write(line) # new line characters should already be there

	out.close()