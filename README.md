# Pattern Lab Module

## Introduction

Pattern lab is a module which allows a SilveStripe developer to have a single place where they can define static HTML with which to test against. Often in project an element on a page may have multiple states or variances which need to be browser tested for, however since the CMS is database driven there isn't any gurantee that every instance possible will be covered in the existing content. Pattern lab's allow you to create static HTML for each of these variances in order to record and test for each one.

You can read more about pattern labs here: http://bradfrostweb.com/blog/post/atomic-web-design/

## Using this module

Create a pattern folder under your templates directory:
templates
 - Patterns

Then begin creating pattern templates in this folder such as:
templates
 - Patterns
 	- Pattern1
 	- Pattern2

These will turn up on the index page automatically at /patterns and you will be able to click through to the pattern which will then render the pattern's template.

As these are not pages `<head>` tags will need to be included in each file we generally recommend the following:
```
<% include Head %>
Some Content
<% include Foot %>
```

Use the includes above in your Page.ss to ensure there's consistency in the head markup, doctype etc.

## Custom Lab Controller

Optionally extend `PatternLab_Controller` with a name such as `MyPatternLab` like so:
```php
class MyPatternLab extends PatternLab {

}
```

This lets you add an init function like your Page.php's init function to ensure they're both including the same CSS and Js.

You'll need to create a routes.yml file to override the existing route config:
```yaml
---
name: routes
After: 'pattern-lab'
---
Director:
  rules:
    'patterns//$Action/$ID/$Name': 'MyPatternLab'
```
