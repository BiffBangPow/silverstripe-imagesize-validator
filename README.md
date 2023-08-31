# Silverstripe image size validator

This extends the standard upload validator and provides additional checks on the dimensions of an uploaded image.

The validator can be applied directly to an uploadfield:

```php
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->addFieldsToTab('Root.Main', [
            UploadField::create('Image')->setValidator(PixelSizeValidator::create()),
        ]);

        return $fields;
    }
```

**Note:** the validator should only be added to upload fields where the underlying class is an `Image` or subclass thereof.   Adding the validator to an upload field where files may be uploaded will cause any non-images to be rejected.