import string

# Configuration des pins microrupteurs
pins_data_count = [8, 10]
# Configuration des pins LCD
pins_data_lcd = [33, 31, 29, 23]
# URL du site web
url = "http://timetoeat.alwaysdata.net/index.php"
# Alphabet pour le chiffrement des données
alpha = string.ascii_letters + string.digits

# Definition du proxy pour les requetes
proxyDict = {
    "http": "http://10.129.254.254:3128",
    "https": "https://10.129.254.254:3128"
}

logo = ( # Logo TimeToEat
    0b00000,
    0b01110,
    0b10101,
    0b10111,
    0b10001,
    0b01110,
    0b00000,
    0b00000
)

e_aigu = ( # Caractère "é"
    0b00010,
    0b00100,
    0b01110,
    0b10001,
    0b11111,
    0b10000,
    0b01110,
    0b00000
)

arrow_up = ( # Caractère Flèche haute
    0b00000,
    0b00100,
    0b00100,
    0b01110,
    0b01110,
    0b11111,
    0b11111,
    0b00000
)

arrow_down = ( # Caractère Flèche basse
    0b00000,
    0b11111,
    0b11111,
    0b01110,
    0b01110,
    0b00100,
    0b00100,
    0b00000
)

arrow_none = ( # Caractère Flèche neutre
    0b00000,
    0b00000,
    0b00000,
    0b11111,
    0b11111,
    0b00000,
    0b00000,
    0b00000
)
