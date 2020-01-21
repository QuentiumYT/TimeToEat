import random, requests

# Definition du proxy pour les requetes
proxyDict = {
    "http": "http://10.129.254.254:3128",
    "https": "https://10.129.254.254:3128"
}

# Alphabet pour le chiffrement des données
alpha = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"

# Algorythme de chiffrement
def cryptage(chaine, cle):
    global alpha
    liste = ""
    for caractere in chaine:
        try:
            index = alpha.index(caractere)
            numero = index + cle
            cle = cle + cle
            if cle > len(alpha) - 1:
                cle = cle - len(alpha) - 1
            if numero > len(alpha) - 1:
                numero = numero - len(alpha)
            liste += alpha[numero]
        except:
            liste += caractere
    return liste

# Valeurs et débit aléatoires
count_people = random.randint(300, 600)
count_debit = random.randint(8, 12)
aes = random.randint(10, len(alpha))

# URL du site web
url = "http://timetoeat.alwaysdata.net/index.php"

# Chiffrement de toutes les données
count_crypt = cryptage(str(count_people), aes)
debit_crypt = cryptage(str(count_debit), aes)
payload = "?count=" + count_crypt + "&debit=" + debit_crypt + "&key=" + str(aes)
requests.get(url + payload, proxies=proxyDict)
print("Requêtes envoyées")
