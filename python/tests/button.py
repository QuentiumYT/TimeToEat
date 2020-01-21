import RPi.GPIO as GPIO
from datetime import datetime
import threading, time

GPIO.setwarnings(False)
GPIO.setmode(GPIO.BOARD)
GPIO.setup([8, 10], GPIO.IN, pull_up_down=GPIO.PUD_DOWN)

count_people = 0
signal_button = 1
count_people_leave = 0

def every_minute():
    global count_people
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
        if timer_finished == clock:
            print(count_people_leave, "people per minute")
            #Send request
            count_people_leave == 0
        time.sleep(1)

thread = threading.Thread(target=every_minute)
thread.start()


def on_join():
    global count_people
    count_people += 1
    print("Join ({} people waiting)".format(count_people))

def on_leave():
    global count_people, count_people_leave
    count_people -= 1
    count_people_leave += 1
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
