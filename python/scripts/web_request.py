import random, requests

proxyDict = {
    "http": "http://10.129.254.254:3128",
    "https": "https://10.129.254.254:3128",
    "ftp": "ftp://10.129.254.254:3128"
}

alpha = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"

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

for i in range(3):
    if i < 5:
        count_people = random.randint(50, 75)
    elif i < 40:
        count_people = random.randint(30, 55)
    elif i < 60:
        count_people = random.randint(400, 700)
    elif i < 70:
        count_people = random.randint(350, 580)
    elif i < 80:
        count_people = random.randint(300, 460)
    elif i < 95:
        count_people = random.randint(250, 350)
    elif i < 120:
        count_people = random.randint(25, 75)
    else:
        count_people = 0
    count_debit = random.randint(8, 12)
    url = "http://timetoeat.alwaysdata.net/index.php"

    aes = random.randint(10, len(alpha))
    count_crypt = cryptage(str(count_people), int(aes))
    debit_crypt = cryptage(str(count_debit), int(aes))
    payload = "?count1=" + count_crypt + "&debit1=" + debit_crypt + "&key1=" + str(aes)
    requests.get(url + payload)

print("Requests sent")
