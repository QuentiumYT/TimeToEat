import RPi.GPIO as GPIO
import time

# Définir les pins de la Raspberry
GPIO.setmode(GPIO.BOARD)
GPIO.setup([8, 10], GPIO.IN, pull_up_down=GPIO.PUD_DOWN)

while True:
    # Pin d'entrée
    if GPIO.input(8) == True:
        print('Button 1 Pressed')
        time.sleep(0.2)
    # Pin de sortie
    elif GPIO.input(10) == True:
        print('Button 2 Pressed')
        time.sleep(0.2)
