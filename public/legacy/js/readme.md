# Editing árMS JSON data files
Wow! You can now live edit your branch's data and online joining form *yourself*. 🎉

## Contents

**[branches.json](https://github.com/guym4c/iwgb-arMS/tree/arMS-refactor/js#branchesjson)**: Information about how branches are portrayed on the website and what is in their joining form.  
**[pages.json](https://github.com/guym4c/iwgb-arMS/tree/arMS-refactor/js#pagesjson)**: Defines the layout of various dynamic pages that go beyond text and images and use custom elements (such as the homepage).

## branches.json
This file contains everything you need. All the data about your branch's join form, contact details and information displayed on **iwgb.co.uk** is stored in here. Below you'll find the structure of this file, and what changes what.

### What it all means
Each branch has their own part of the file, which looks like this.

```javascript
"couriers": {
	"name": "couriers",
	"display": "Couriers & Logistics",
	"desc": "The companies...",
	"website": { 
		... 
	},
	"costs": { 
		... 
	},
	"fields": [
		...
	]
```

`name`:  The shortname of the branch. No spaces or capitals. Must match the name of your branch given at the start.  
`display`:  How your branch name displays.  
`desc`:  A short and snappy description. 🔥

#### website
This contains info about where your branch's personal website can be found.  
`type`: At the moment, you can only have `external`.  
`link`: A link over to your website.

#### costs
Used in your join form, these are the different membership types and prices that new members can choose when they sign up. Here's the couriers' one as an example.
```javascript
"costs": {
	"citysprint-absolutely-ecourier-pushbikes": {
		"name": "citysprint-absolutely-ecourier-pushbikes",
		"display": "Pushbikes at CitySprint, Absolutely and eCourier",
		"amount": 9,
		"link": "http://link to payment page here"
	},
	"other": {
		"name": "other",
		"display": "Other",
		"amount": 6,
		"link": "http://link to payment page here"
	}
},
```
`name`: No caps, no spaces as before. **Must** be the same as the name of the cost on the first line, for reasons.  
`display`: You get this part now. How it looks on the outside. (Descriptive, hopefully. If you need a ton more space you can add a `label` field - more on that below.)  
`amount`: Monthly cost, in pounds.  
`link`: The link to your branch's payment page (must start with `http://` or `https://`).

Importantly, you **must** have at least one `cost` - call it something like `all` if you only have one. The website will then pre-select this when users sign up.

#### fields
Here you get to design your own part of your new member form. Exciting, right?!

Inside your `fields` section, each section of curly brackets `{ ... }` represents one item in the form. 

`name` and `display` are as before.
You can also specify `required: true` (self-explanatory).
>`label` type inputs don't need a `name`. `select`, `date`, `month`, `time` and `checkbox` types don't need a `display` - use a `label` if you want to add something.

##### Input types
This is the `type` option. You can have loads of different ones -

`text`: Do I need to explain what this is?  
`number`, : Like a `text`, but for numbers.  
`label`: You can't write anything in a label. It just says its `display`. (Also, doesn't need a `name`).  
`date`, `month`, `time`: Various date and time inputs.  
`textarea`: Use this for a bigger text box with more than 1 line - you'll need to specify how many lines you want with a `size`.  
`checkbox`: A box you can tick.  
`select`: A drop-down box. You'll need to include some `options` (see the example below).  

Again, here's part of the couriers' `fields` section as an example. 

```javascript
"fields": [
	{
		"name": "callsign",
		"display": "Callsign/ID",
		"type": "text",
		"required": true
	},
	{
		"name": "pay-scheme",
		"display": "Pay scheme",
		"type": "select",
		"required": true,
		"options": [
			{
				"name": "hourly",
				"display": "Hourly" 
			},
			{
				"name": "per-drop",
				"display": "Per drop/piece rate"
			}
		]
	}
]
```

## pages.json
This is just a list of `elements` for each page, and contains its content. The different element types are described below.

### slant-box
```javascript
"type": "slant-box"
```

`color`: The background colour of the element. Either `dark`, `light` or `red`.  
`direction`: Which side of the box the text is displayed. Can be `left` or `right`.  

The following fields are used in this element, and are all required:  
`heading`: A text field. Self-explanatory.  
`body`: A markdown field. The main text of the box.  
`image`: A link to the raw image to be displayed. Must be fully-qualified if external (e.g. you may have `/img/something.jpg` if it's on the IWGB site, but otherwise go with `https://blahblah.com/blah.jpg`).  
`links`: A list of links, stored in `items`. Each one has a `display` (the link text) and an `href` (the link to go to).  

### tri-promo
```javascript
"type": "tri-promo"
```

`backgroundType`: Can be either a `color` or an `image`.  
`backgroundAsset`: The colour, if you chose that (at the moment you can only have `red`), or the link to the image.

The following fields are used in this element:  
`heading`: Text, self-explanatory, required.  
`subheading`: Text.  
`intro`: The main paragraph at the top of the element, in markdown.  
`mainActionLink`: If you want a main link at the top of the columns, you'll need some `content` and an `href` (the hyperlink itself) in here.  

The next bit, `columns`, defines what's in each. They're named `column1`, `column2`, etc. All fields within this are required:  
`icon`: The full FontAwesome icon string (e.g. `fas fa-shield`).  
`heading`: Text.  
`body`: The main content of the column, in markdown.  
`link`: The link at the bottom of the column (`content` and `href`, as above).
