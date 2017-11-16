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
   <type>link</type>
   <icon>https://localhost/external.svg</icon>
  </element>
 </data>
</ocs>
```

#### Explanation

| Field | Type   | Description                              |
| ----- | ------ | ---------------------------------------- |
| id    | int    | Numeric identifier of the site           |
| name  | string | Name of the site, ready to use           |
| url   | string | URL that should be framed/linked to      |
| type  | string | Can be one of `link`, `settings` or `quota`; see [this issue](https://github.com/nextcloud/external/issues/7) for details |
| icon  | string | Full URL of the icon that should be shown next to the name of the link |

### ETag / If-None-Match

The API provides an ETag for the sites array. In case the ETag matches the given value, a `304 Not Modified` is delivered together with an empty response body.

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
     <element>device</element>
     <element>groups</element>
     <element>redirect</element>
    </v1>
   </external>
   ...
```
