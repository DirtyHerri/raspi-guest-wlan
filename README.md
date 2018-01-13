# Raspi Guest WLAN
Modified Version of c't 26/2017, S. 154

Credits:   
Mirko Dölle  
https://www.heise.de/select/ct/2017/26/1513638287074706

## Prepare Your Raspi

### Apache

Install Apache and don't forget ```sudo a2enmod rewrite```

Ensure, that .htaccess is allowed to override settings:

```
<Directory /var/www/html/public/>
    AllowOverride all
    Order allow,deny
    Allow from all
    Require all granted
</Directory>
```

### Firewall

Install [iptables-persistent](https://www.google.de/search?q=iptables-persistent)

```sudo apt-get install iptables-persistent```

Copy ```etc/iptables/rules.v4``` to ```/etc/iptables/rules.v4``` 
or apply the rules inside manually and call ```iptables-save```.

The following iptables rule inside ```etc/iptables/rules.v4```
blocks all traffic from public to private subnet:
   
```-A FORWARD -d 192.168.2.0/24 -i wlan0 -o eth0 -j DROP```   

Change ```192.168.2.0/24``` to your own private subnet.
Or remove that rule, to allow communication between guests and your devices
inside the private network.

## Script Configuration

### Admin Page (PIN Page)

If you open the captive page from inside your private subnet, it will show the PIN (if there is one),
instead of the login form. To enable the script, to identify the private subnet, it has to be defined in
the configuration script ```php/src/config.php```.

Leave out the last byte for the ```'subnet_private'``` entry (e.g,  ```'192.168.2.'```). 
The script just compares the IP addresses as strings... no magic there.

#### PIN Display

See files in ```wlanpindisplay``` if you want to display the PIN on a LC Display.

#### Execute Shell Scripts via Web UI

Some scripts need sudo privileges.
Run ```visudo``` and add the following lines
to enable the apache user to execute those scripts.
``` 
www-data ALL = NOPASSWD: /usr/local/bin/togglewlan  
www-data ALL = NOPASSWD: /usr/local/bin/powerctl
```

### Read The Code!

Go ahead and read the code, in order to understand whats going on.

Feel free to fork this repository and alter things to fit your needs :)

## Appendix

***from original README:***


Gäste-WLAN mit Komfort
Raspberry Pi als offener WLAN-Router mit Captive Portal

c't 26/2017, S. 154

#### Netzwerkkonfiguration
 - network/interfaces  
 
Installation:  
 ```
 sudo cp network/interfaces /etc/network/interfaces
 ```  

#### HostAP-Konfiguration als offener WLAN Access Point
 - default/hostapd
 - hostapd/hostapd.conf  

Installation:  
 ```
 sudo apt-get install hostapd  
 sudo cp default/hostapd /etc/default  
 sudo cp hostapd/hostapd.conf /etc/hostapd
 ```  

#### DNSmasq-Konfiguration, arbeitet als DHCP-Server für 192.168.255.0/24
 - default/dnsmasq
 - dnsmasq.conf                   

Installation:                        
```      
sudo apt-get install dnsmasq
sudo cp default/dnsmasq /etc/default
sudo cp dnsmasq.conf /etc
```
