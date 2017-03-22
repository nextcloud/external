# External sites

This application allows an admin to add a link in the Nextcloud web interface
Apps menu that points to an external website. By simply entering the URL and
the name for the external site, an icon appears. When this icon is clicked by a
user, the external website appears in the Nextcloud frame. For the user, this
external site appears as if it is part of Nextcloud but, in fact, this can be
any external URL.

## OCS API

It is also possible to get the sites via an OCS endpoint. The request must be authenticated.
Only sites for the user´s language are returned:
```bash
curl  -H "OCS-APIRequest: true" \
  https://admin:admin@localhost/ocs/v2.php/apps/external/api/v1
```

### Response
```xml
<?xml version="1.0"?>
<ocs>
 <meta>
  <status>ok</status>
  <statuscode>200</statuscode>
  <message>OK</message>
 </meta>
 <data>
  <element>
   <id>23</id>
   <name>Homepage</name>
   <url>https://localhost/index.php</url>
   <lang>en</lang>
   <icon>external.svg</icon>
  </element>
 </data>
</ocs>
```

### Capability

The app registers a capability, so clients can check that before making the actual OCS request:
```xml
<?xml version="1.0"?>
<ocs>
 ...
 <data>
  <capabilities>
   ...
   <external>
    <v1>
     <element>sites</element>
    </v1>
   </external>
   ...
```
