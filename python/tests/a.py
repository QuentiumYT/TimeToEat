from RPLCD import CharLCD
from RPi import GPIO
from datetime import datetime
import threading, time

GPIO.setwarnings(False)
GPIO.setmode(GPIO.BOARD)
GPIO.setup([8, 10], GPIO.IN, pull_up_down=GPIO.PUD_DOWN)

lcd = CharLCD(numbering_mode=GPIO.BOARD, cols=16, rows=2, pin_rs=37, pin_e=35, pins_data=[33, 31, 29, 23], charmap="A02")

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

up = (
    0b00000,
    0b00100,
    0b00100,
    0b01110,
    0b01110,
    0b11111,
    0b11111,
    0b00000
)

down = (
    0b00000,
    0b11111,
    0b11111,
    0b01110,
    0b01110,
    0b00100,
    0b00100,
    0b00000
)

none = (
    0b00000,
    0b00000,
    0b00000,
    0b11111,
    0b11111,
    0b00000,
    0b00000,
    0b00000
)

count_people = count_people_leave = 0
old_nb_people = old_nb_flow = 0
signal_button = 1
count_people_leave = 0
lcd.create_char(0, logo)
lcd.create_char(1, e_aigu)
lcd.create_char(2, up)
lcd.create_char(3, down)
lcd.create_char(4, none)

lcd.clear()
lcd.cursor_pos = (0, 0)
lcd.write_string("Personnes: ---")
lcd.cursor_pos = (1, 0)
lcd.write_string("D\1bit: ---/min")
lcd.write_string(" \0")

def every_minute():
    global count_people, count_people_leave
    now = datetime.today().replace(microsecond=0)
    clock = now.replace(day=now.day, hour=now.hour, minute=now.minute, second=0, microsecond=0)
    if now.minute == clock.minute:
        clock = now.replace(day=now.day, hour=now.hour, minute=now.minute + 1, second=0, microsecond=0)
    while True:
        time_now = datetime.today().replace(microsecond=0)
        timer_finished = time_now
        sec_time = int(time_now.strftime("%S"))
        if sec_time > 55 and sec_time <= 59:
            timer_finished = time_now.replace(minute=time_now.minute + 1, second=0)
        print(clock, timer_finished)
        if timer_finished == clock:
            print(count_people_leave, "people per minute")
            write_on_lcd(None, count_people_leave, None)
            count_people_leave = 0
            now = datetime.today().replace(microsecond=0)
            clock = now.replace(minute=now.minute + 2, second=0, microsecond=0)
            print(clock)
        time.sleep(5)

thread = threading.Thread(target=every_minute)
thread.start()

def write_on_lcd(nb_people, nb_flow, state):
    global old_nb_people, old_nb_flow
    lcd.clear()
    lcd.cursor_pos = (0, 0)
    if not state == None:
        if state == "+":
            state = " \2"
        elif state == "-":
            state = " \3"
    else:
        state = " \4"
    if not nb_people == None:
        lcd.write_string("Personnes: " + str(nb_people) + state)
        old_nb_people = nb_people
    else:
        lcd.write_string("Personnes: " + str(old_nb_people) + state)
    lcd.cursor_pos = (1, 0)
    if not nb_flow == None:
        lcd.write_string("D\1bit: {}/sec".format(str(nb_flow)))
        old_nb_flow = nb_flow
    else:
        lcd.write_string("D\1bit: {}/sec".format(str(old_nb_flow)))
    lcd.write_string(" \0")

def on_join():
    global count_people
    count_people += 1
    write_on_lcd(count_people, None, "+")
    print("Join ({} people waiting)".format(count_people))

def on_leave():
    global count_people, count_people_leave
    count_people -= 1
    count_people_leave += 1
    write_on_lcd(count_people, None, "-")
    print("Leave ({} people waiting)".format(count_people))

while True:
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
