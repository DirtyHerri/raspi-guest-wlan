# Raspi Guest WLAN
Modified Version of c't 26/2017, S. 154

Credits:   
Mirko Dölle  
https://www.heise.de/select/ct/2017/26/1513638287074706

## Configuration

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

The following iptables rule inside ```etc/iptables/rules.v4```
blocks all traffic from public to private subnet:
   
```-A FORWARD -d 192.168.2.0/24 -i wlan0 -o eth0 -j DROP```   

Change ```192.168.2.0/24``` to your own private subnet.
Or remove that rule, to allow communication between guests and your devices
inside the private network.

*See also: [iptables-persistent](https://www.google.de/search?q=iptables-persistent)*

### Admin Page (PIN Page)

If you open the captive page from inside your private subnet, it will show the PIN (if there is one),
instead of the login form. To enable the script, to identify the private submit, it has to be defined in
the configuration script ```php/src/config.php```.

Leave out the last byte for the ```'SUBNET_PRIVATE'``` entry (e.g,  ```'192.168.2.'```). The script just compares the IP addresses as strings...
no magic there.

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
