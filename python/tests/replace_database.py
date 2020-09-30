import os, dotenv, mysql.connector

dotenv.load_dotenv("../.env")

SQL_HOST = os.environ.get("SQL_HOST")
PWD = os.environ.get("PWD")

try:
    mySQLconnection = mysql.connector.connect(host=SQL_HOST,
                                              database="timetoeat_bdd",
                                              user="timetoeat",
                                              password=PWD)
    cursor = mySQLconnection.cursor()
    cursor.execute("select * from now_time")
    records = cursor.fetchall()
    print("Total number of rows is - ", cursor.rowcount)
    for row in records:
        print("id =", row[0])
        print("nb_personne =", row[1])
        print("temps =", row[2])
        print("debit =", row[3], "\n")
        new = round(row[1] / 1000)
        cursor.execute("update now_time set `nb_personne` = " + str(new) + " where id = " + str(row[0]))
        mySQLconnection.commit()
        if row[0] == 14:
            break
    cursor.close()

except mysql.connector.Error as e:
    print("Error while connecting to MySQL", e)
finally:
    if(mySQLconnection.is_connected()):
        mySQLconnection.close()
