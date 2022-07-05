# wp-real-nonce
Simple NONCE implementation for WordPress<br>
Nonce is a token to be used only once. But WordPress's nonce can be used several times in its lifesapn. So I created this to be able to use real nonce on my WordPress projects.
## Usage
```php
$realNonce = new WpRealNonce();
$nonceToken = $realNonce->create('nonce_name');
```
#### </br>Get hidden form field:
```php
echo $realNonce->field('nonce_name');
```
#### </br>Validate the nonce:
```php
$realNonce->check('nonce_name', 'nonce_value');
```

#### </br>Store specific nonce with a name:
```php
$realNonce->store('nonce_value', 'nonce_name');
```
#### </br>Delete specific nonce with name:
```php
$realNonce->delete('nonce_name');
```
#### </br>Clear all nonces older than 1 day:
```php
$realNonce->clear();

//if you want to clear all nonces ever, use:
$realNonce->clear(true);
```
