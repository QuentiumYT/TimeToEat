import os, dotenv
from ftplib import FTP

dotenv.load_dotenv("../../.env")

FTP_HOST = os.environ.get("FTP_HOST")
PWD = os.environ.get("PWD")

ftp = FTP(FTP_HOST, "timetoeat_admin", PWD)
print(ftp.dir())

f = open("menu.jpg", "rb")
ftp.storbinary("STOR img/menu.jpg", f)
f.close()
