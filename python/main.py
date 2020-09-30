from RPLCD import CharLCD # Module pour l'ecran LCD
from RPi import GPIO # Module pour les pins
from datetime import datetime # Module pour le temps (timer)
import threading, time, calendar, requests, random # Autres modules complémentaires

from constantes import * # Importation des constantes / pins / charactères spéciaux

# Réglages pour GPIO
GPIO.setwarnings(False)
GPIO.setmode(GPIO.BOARD)
GPIO.setup(pins_data_count, GPIO.IN, pull_up_down=GPIO.PUD_DOWN) # Mode des microrupteurs

# Définition de l'écran LCD
lcd = CharLCD(numbering_mode=GPIO.BOARD,
              cols=16, rows=2, pin_rs=37, pin_e=35,
              pins_data=pins_data_lcd, charmap="A02")

# Fonction de cryptage
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

# Affichage des données sur l'écran LCD
def write_on_lcd(nb_people, nb_flow, state):
    global old_nb_people, old_nb_flow
    lcd.clear()
    lcd.cursor_pos = (0, 0) # Position initiale
    if state == "+": # Personne rejoins
        state = " \2"
    elif state == "-": # Personne quitte
        state = " \3"
    elif state == "*": # Débit affiché
        state = " \4"
    if not nb_people == "None":
        lcd.write_string("Personnes: " + str(nb_people) + state)
        old_nb_people = nb_people #Définition de l'ancienne valeur des personnes
    else:
        lcd.write_string("Personnes: " + str(old_nb_people) + state)
    lcd.cursor_pos = (1, 0)
    if not nb_flow == "None":
        lcd.write_string("D\1bit: {}/min".format(str(nb_flow)))
        old_nb_flow = nb_flow # Définition de l'ancienne valeur du débit
    else:
        lcd.write_string("D\1bit: {}/min".format(str(old_nb_flow)))
    lcd.write_string(" \0")

def init():
    global count_people, count_people_leave, count_people_leave_now, old_nb_people, old_nb_flow, signal_button
    # Définition des variables par défaut
    count_people = count_people_leave = 0
    count_people_leave_now = 0
    old_nb_people = old_nb_flow = 0
    signal_button = 0
    # Création des caractères spéciaux
    lcd.create_char(0, logo)
    lcd.create_char(1, e_aigu)
    lcd.create_char(2, arrow_up)
    lcd.create_char(3, arrow_down)
    lcd.create_char(4, arrow_none)
    # Initialisation de l'écran LCD
    lcd.clear()
    lcd.cursor_pos = (0, 0)
    lcd.write_string("Personnes: --")
    lcd.cursor_pos = (1, 0)
    lcd.write_string("D\1bit: --/min")
    lcd.write_string(" \0")

# Définition du timer en thread pour gérer le comptage et envoyer les données
def timer_every_minute():
    global count_people, count_people_leave, count_people_leave_now
    now = datetime.today().replace(microsecond=0)
    num_days_month = calendar.monthrange(int(now.strftime("%y")), int(now.strftime("%m")))[1]
    clock = now.replace(day=now.day, hour=now.hour, minute=now.minute, second=0, microsecond=0)
    if now.minute == clock.minute:
        if not int(now.minute) == 59:
            clock = now.replace(day=now.day, hour=now.hour, minute=now.minute + 1, second=0, microsecond=0)
        else:
            clock = now.replace(day=now.day, hour=now.hour + 1, minute=0, second=0, microsecond=0)

    while True:
        time_now = datetime.today().replace(microsecond=0)
        timer_finished = time_now
        sec_time = int(time_now.strftime("%S"))
        min_time = int(time_now.strftime("%M"))
        hour_time = int(time_now.strftime("%H"))
        day_time = int(time_now.strftime("%d"))
        if sec_time > 50 and sec_time <= 59:
            if min_time == 59:
                if hour_time == 23:
                    if day_time == num_days_month:
                        timer_finished = time_now.replace(month=time_now.month + 1, day=1, hour=0, minute=0, second=0)
                    else:
                        timer_finished = time_now.replace(day=time_now.day + 1, hour=0, minute=0, second=0)
                else:
                    timer_finished = time_now.replace(hour=time_now.hour + 1, minute=0, second=0)
            else:
                timer_finished = time_now.replace(minute=time_now.minute + 1, second=0)
        print(clock, time_now)
        if timer_finished == clock:
            print(count_people_leave, "people per minute")
            write_on_lcd("None", count_people_leave, "*")
            #send_data_web(count_people, count_people_leave)
            count_people_leave = 0 # Reset du débit
            now = datetime.today().replace(microsecond=0)
            if min_time == 59:
                if hour_time == 23:
                    clock = now.replace(day=now.day + 1, hour=0, minute=1, second=0, microsecond=0)
                else:
                    clock = now.replace(hour=now.hour + 1, minute=1, second=0, microsecond=0)
            else:
                clock = now.replace(minute=now.minute + 2, second=0, microsecond=0)
        time.sleep(10)
        send_data_web_now(count_people, count_people_leave_now)
        count_people_leave_now = 0

# Fonction d'envoi de données pour un cycle cantine
def send_data_web(count_people, count_debit):
    random_key = random.randint(10, len(alpha)) # Clé aléatoire
    count_crypt = cryptage(str(count_people), int(random_key)) # Cryptage du nombre de personnes
    debit_crypt = cryptage(str(count_debit), int(random_key)) # Cryptage du nombre du débit
    payload = "?count=" + count_crypt + "&debit=" + debit_crypt + "&key=" + str(random_key) # Données dans l'URL
    requests.get(url + payload, proxies=proxyDict) # Envoi des données avec proxy

# Fonction d'envoi de données en temps réel
def send_data_web_now(count_people, count_debit):
    random_key = random.randint(10, len(alpha)) # Clé aléatoire
    count_crypt = cryptage(str(count_people), int(random_key)) # Cryptage du nombre de personnes
    debit_crypt = cryptage(str(count_debit), int(random_key)) # Cryptage du nombre du débit
    payload = "?count_now=" + count_crypt + "&debit_now=" + debit_crypt + "&key_now=" + str(random_key) # Données dans l'URL
    requests.get(url + payload, proxies=proxyDict) # Envoi des données avec proxy

# Définition du thread pour gérer le timer et le comptage de données en même temps
def start_thread():
    thread = threading.Thread(target=timer_every_minute)
    thread.daemon = True # Deamon permet d'arreter le thread en cas de CTRL-C
    thread.start()

    def on_join(): # Simple comptage
        global count_people
        count_people += 1
        write_on_lcd(count_people, "None", "+")
        print("1 people joined ({} people waiting)".format(count_people))

    def on_leave(): # Décomptage + débit
        global count_people, count_people_leave, count_people_leave_now
        if not count_people <= 0:
            count_people -= 1
            count_people_leave += 1
            count_people_leave_now += 1
            write_on_lcd(count_people, "None", "-")
            print("1 people left ({} people waiting)".format(count_people))
        else:
            count_people = 0
            write_on_lcd(count_people, "None", "*")

    while True: # Boucle infinie pour relever les données
        time.sleep(0.001)
        if GPIO.input(8):
            if signal_button == 1 or signal_button == 3:
                signal_button = 2
                on_join()
        elif GPIO.input(10):
            if signal_button == 1 or signal_button == 2:
                signal_button = 3
                on_leave()
        else:
            signal_button = 1

if __name__ == "__main__":
    init()
    # Démarrage du thread pour le timer en meme temps que le comptage
    start_thread()
