import lcd
import sys
sys.path.append('/home/pi/lcd')
lcd.lcd_init()
lcd.lcd_byte(lcd.LCD_LINE_1, lcd.LCD_CMD)
lcd.lcd_string("Salut", 2)
lcd.lcd_byte(lcd.LCD_LINE_2, lcd.LCD_CMD)
lcd.lcd_string("Nicolas !", 1)
lcd.GPIO.cleanup()
