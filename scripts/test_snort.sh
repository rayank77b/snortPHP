#
## m    h  dom mon dow command
## */15 *  *   *   *   /bin/sh /usr/home/rootaf/test > /dev/null
rhlx08:/var# cat /usr/home/rootaf/test
#!/bin/sh

ps afx | fgrep -v grep | fgrep '/usr/sbin/snort'

if [  "$?" -eq "0" ]
then
echo "ok running" 
else
/etc/init.d/snort start
echo $(date) "   Starting Snort, was not running" >> /usr/home/rootaf/snort.test.log
fi

