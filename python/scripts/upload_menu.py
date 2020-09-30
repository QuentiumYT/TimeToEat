import os, dotenv, urllib.request
from pdf2image import convert_from_path
from ftplib import FTP

dotenv.load_dotenv("../.env")

FTP_HOST = os.environ.get("FTP_HOST")
PWD = os.environ.get("PWD")

# Chercher le menu depuis le site du lycée et le télécharger
url = "http://www.lyc-heinrich-haguenau.ac-strasbourg.fr/docman-documents/menus-de-la-restauration-scolaire/"
content_page = urllib.request.urlopen(url).read()
data_url_menu = str(content_page).split("restauration-scolaire/")[-1].split(" data-title=")[0]
urllib.request.urlretrieve(url + data_url_menu, "menu.pdf")

# Convertir le menu en JPEG
pages = convert_from_path("menu.pdf", 500)
for page in pages:
    page.save("menu.jpg", "JPEG")

# Connection au FTP de TimeToEat et upload l'image
ftp = FTP(FTP_HOST, "timetoeat_admin", PWD)
f = open("menu.jpg", "rb")
ftp.storbinary("STOR img/menu.jpg", f)
f.close()

# Supression des fichiers temporaires
os.remove("menu.pdf")
os.remove("menu.jpg")
