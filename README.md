# wp-real-nonce
Simple NONCE implementation for WordPress<br>
Nonce is a token to be used only once. But WordPress's nonce can be used several times in its lifesapn. So I created this to be able to use real nonce on my WordPress projects.
## Usage
```php
$realNonce = new WpRealNonce();
$nonceToken = $realNonce->create('nonce_name');
```
To get hidden fields:
```php
echo $realNonce->field('nonce_name');
```
To validate the nonce:
```php
$realNonce->check('nonce_name', 'nonce_value');
```
