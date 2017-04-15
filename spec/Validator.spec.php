<?php

namespace Kocal\Spec;

use Kocal\Validator\Validator;
use Symfony\Component\HttpFoundation\File\UploadedFile;

describe(Validator::class, function () {
    it('should validate', function () {
        $validator = new Validator([
            'foo' => 'required|min:4',
            'users.*.id' => 'required|distinct|integer',
            'users.*.email' => 'required|distinct|email',
        ]);
        $validator->validate([
            'foo' => 'hello',
            'users' => [
                ['id' => 1, 'email' => 'john@example.com'],
                ['id' => 2, 'email' => 'smith@example.com'],
            ]
        ]);

        expect($validator->passes())->toBeTruthy();
        expect($validator->errors()->toArray())->toBeEmpty();
    });

    it('should not validate', function () {
        $validator = new Validator([
            'foo' => 'required'
        ]);
        $validator->validate([]);

        expect($validator->fails())->toBeTruthy();
        expect($validator->errors()->toArray())->toBe([
            'foo' => ["Le champ foo est obligatoire."],
        ]);
    });

    it('should use custom error messages', function () {
        $validator = new Validator([
            'foo' => 'required',
            'bar' => 'min:10'
        ]);
        $validator->validate(['bar' => 'foo'], [
            'required' => 'Le champ :attribute est réellement obligatoire.',
            'bar.min' => "Un message d'erreur customisé."
        ]);

        expect($validator->fails())->toBeTruthy();
        expect($validator->errors()->toArray())->toBe([
            'foo' => ["Le champ foo est réellement obligatoire."],
            'bar' => ["Un message d'erreur customisé."],
        ]);
    });

    it('should use custom attributes', function () {
        $validator = new Validator(['foo' => 'required']);
        $validator->validate([], [], ['foo' => 'FOO']);

        expect($validator->fails())->toBeTruthy();
        expect($validator->errors()->toArray())->toBe([
            'foo' => ["Le champ FOO est obligatoire."]
        ]);
    });

    it('should use custom rule', function () {
        $validator = new Validator(['foo' => 'is_foo']);
        $validator->extend('is_foo', function ($attribute, $value, $parameters, $validator) {
            return $value == 'foo';
        }, "Le champ :attribute n'est pas égal à 'foo'.");
        $validator->validate([
            'foo' => 'not_foo'
        ]);

        expect($validator->fails())->toBeTruthy();
        expect($validator->errors()->toArray())->toBe([
            'foo' => ["Le champ foo n'est pas égal à 'foo'."]
        ]);
    });

    it('should use english translation', function () {
        $validator = new Validator(['foo' => 'max:3', 'email' => 'email'], 'en');
        $validator->validate(['foo' => 'hello', 'email' => 'not-an-email']);

        expect($validator->fails())->toBeTruthy();
        expect($validator->errors()->toArray())->toBe([
            'foo' => ["The foo may not be greater than 3 characters."],
            'email' => ["The E-mail address must be a valid email address."]
        ]);
    });

    it('should validate uploaded file', function () {
        $uploadedFile = new UploadedFile(__DIR__ . '/fixtures/image.png', null, null, null, null, true);
        $validator = new Validator(['a' => 'file|dimensions:width=20,height=20']);
        $validator->validate(['a' => $uploadedFile]);

        expect($validator->passes())->toBeTruthy();
    });
});