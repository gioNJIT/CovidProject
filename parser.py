import xml.dom.minidom
import sys
import mysql.connector
from datetime import date


#mycursor.execute("CREATE TABLE coronastat (state VARCHAR(25),cases INT(255),deaths INT(255))")

###########parsing document and splitting into list###############
document = xml.dom.minidom.parse(sys.argv[1])
data = []
tableElements = document.getElementsByTagName('table')
for tr in tableElements[0].getElementsByTagName('tr'):
    
    for td in tr.getElementsByTagName('td'):
        for node in td.childNodes:
            if node.nodeType == node.TEXT_NODE:
                data.append(node.nodeValue)

###########connecting to DB#############################    
mydb = mysql.connector.connect(
  host="localhost",
  user="root",
  passwd="",
  database="newDB"
)
mycursor = mydb.cursor()


###########comparing dates to determine how to fill DB#############################   
today = (str(date.today()),)
datecmd="SELECT thedate FROM datetoday"
mycursor.execute(datecmd)
DBdate=mycursor.fetchall()

if (DBdate[0][0]==today[0]):
	for items in range(3,len(data),3):
	
		data[items+1]=data[items+1].replace(",","")
		data[items+1]=int(data[items+1])
		data[items+2]=data[items+2].replace(",","")
		data[items+2]=int(data[items+2])
		#sqlcmd = "INSERT INTO coronastat (state, cases, deaths) VALUES (%s,%s,%s)"
		sqlcmd = "UPDATE coronastat SET state= "+"'"+ data[items]+"', cases ="+"'"+ str(data[items+1])+"', deaths ="+"'"+ str(data[items+2])+"' WHERE state = '"+data[items]+"'"
		sqlvals = (data[items],data[items+1],data[items+2])
		mycursor.execute(sqlcmd)		
	mycursor.execute("SELECT * FROM coronastat")
	myresult = mycursor.fetchall()
	for x in myresult:
	  print(x)
	  
else:
	print ("PLACING DATE IN DB")
	setdate="UPDATE datetoday SET thedate = "+"'"+ today[0]+"'"
	mycursor.execute(setdate)
	copy_case="UPDATE coronastat SET yest_case = cases"
	mycursor.execute(copy_case)
	copy_death="UPDATE coronastat SET yest_DEATH = deaths"
	mycursor.execute(copy_death)
	for items in range(3,len(data),3):
		data[items+1]=data[items+1].replace(",","")
		data[items+1]=int(data[items+1])
		data[items+2]=data[items+2].replace(",","")
		data[items+2]=int(data[items+2])
		#sqlcmd = "INSERT INTO coronastat (state, cases, deaths) VALUES (%s,%s,%s)"
		sqlcmd = "UPDATE coronastat SET state= "+"'"+ data[items]+"', cases ="+"'"+ str(data[items+1])+"', deaths ="+"'"+ str(data[items+2])+"' WHERE state = '"+data[items]+"'"
		sqlvals = (data[items],data[items+1],data[items+2])
		mycursor.execute(sqlcmd)
	mycursor.execute("SELECT * FROM coronastat")
	myresult = mycursor.fetchall()
	for x in myresult:
	  print(x)
	

mydb.commit()









