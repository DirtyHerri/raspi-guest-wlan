# Raspi Guest WLAN
Modified Version of c't 26/2017, S. 154

## [insert instructions here...]


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
