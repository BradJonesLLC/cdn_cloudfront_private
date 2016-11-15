# Amazon Cloudfront CDN private files integration

This is an API module to assist with [serving private/protected content](http://docs.aws.amazon.com/AmazonCloudFront/latest/DeveloperGuide/PrivateContent.html)
through Amazon Cloudfront. It has an implied dependency on CDN module, however there
is no UI and you will need to code specific business rules regarding
what content to protect, and how.

Access may be controlled by signed URLs (query string parameters) or a
signed cookie.

## General principles

When signing URLs, we implement `hook_file_url_alter()`, which normally will be
called after CDN module unless you have manually adjusted module weights.
An event is then emitted to determine if the Uri being altered qualifies
for protection and should be signed.

## Known issues

Once this module signs a URL for private files access, it uses the Drupal
page cache "kill switch," since there's no cache metadata associated
with these rewritten URLs and no support (yet) for that in core. This is
configurable when responding to the event, and it's possible you could set
signed cookies at login, for instance, and keep pages cacheable.

### One approach to protecting content using Flysystem

A sample use case would be to use [Flysystem](https://drupal.org/project/flysystem)
to create a new "protected" stream wrapper to in effect provide a souped-up
version of the private files functionality in Drupal core. CDN module will
refuse to re-write that URL since Flysystem does not consider its streams
"local," however you could build and mark for signing CDN URLs using
the `CdnCloudfrontPrivateEvent`, and then restrict direct access of those
files to only Cloudfront using Custom Origin Headers and an implementation
of `hook_file_download()`:

```php
/**
 * Implements hook_file_download().
 */
function mymodule_file_download($uri) {
  $scheme = Drupal::service('file_system')->uriScheme($uri);
  if ($scheme == 'swift-protected') {
    $request = Drupal::request();
    if ($request->headers->get('X-CDN-Token') != getenv('CDN_TOKEN')) {
      return -1;
    }
  }
  return NULL;
}
```

## Copyright and license.

Copyright 2016 Brad Jones LLC. GPL-2.
