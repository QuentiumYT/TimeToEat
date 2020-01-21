#! /bin/sh
# /etc/init.d/TimeToEat.sh

### BEGIN INIT INFO
# Provides:          TimeToEat.sh
# Required-Start:    $remote_fs $syslog
# Required-Stop:     $remote_fs $syslog
# Default-Start:     2 3 4 5
# Default-Stop:      0 1 6
### END INIT INFO

sleep 10
sudo screen -dmS tte bash -c "sudo python3 /home/pi/main.py; echo Starting TimeToEat!"
