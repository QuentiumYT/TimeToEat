import urllib.request

url_static = "http://www.lyc-heinrich-haguenau.ac-strasbourg.fr/docman-documents/menus-de-la-restauration-scolaire/"
content_page = urllib.request.urlopen(url_static).read()
data_url_menu = str(content_page).split("restauration-scolaire/")[-1].split(" data-title=")[0]
urllib.request.urlretrieve(url_static + data_url_menu, "menu.pdf")
