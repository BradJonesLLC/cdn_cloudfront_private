# Amazon Cloudfront CDN private files integration

## Required changes to .htaccess

There must be a section in the site's .htaccess akin to:
```
  # Custom access control for directories we want served only from Cloudfront.
  RewriteCond %{HTTP_USER_AGENT} !Amazon\wCloudfront
  RewriteCond %{REQUEST_URI} .*\/files\/.*protected.*\/.*
  RewriteRule ^ - [F]
```

## Copyright and license.

Copyright 2016 Brad Jones LLC. GPL-2.
