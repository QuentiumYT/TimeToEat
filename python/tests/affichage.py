from RPLCD import CharLCD
from RPi import GPIO

# Définir les pins de l'écran
lcd = CharLCD(numbering_mode=GPIO.BOARD, cols=16,
              rows=2, pin_rs=37, pin_e=35,
              pins_data=[33, 31, 29, 23], charmap='A02')

# Caractères spéciaux pour le logo et l'accent
logo = (
    0b00000,
    0b01110,
    0b10101,
    0b10111,
    0b10001,
    0b01110,
    0b00000,
    0b00000
)

e_aigu = (
    0b00010,
    0b00100,
    0b01110,
    0b10001,
    0b11111,
    0b10000,
    0b01110,
    0b00000
)

lcd.create_char(0, logo)
lcd.create_char(1, e_aigu)
lcd.clear()
# Afficher les personnes sur la première ligne
lcd.cursor_pos = (0, 0)
lcd.write_string("Personnes: 542")
lcd.cursor_pos = (1, 0)
# Afficher le débit sur la seconde ligne
lcd.write_string("D\1bit: 12/sec")
lcd.write_string(" \0")
