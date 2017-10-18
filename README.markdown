**Time Select** is a fieldtype for ExpressionEngine&reg; 2 and 3 that offers a configurable dropdown menu of times.

## Usage

After installing and activating Time Select, choose it as your fieldtype in either a custom field, Grid field, Matrix field, or Low Variables field. You have two configuration options for each Time Select field:

* **Display Style**: whether to show the time in 12-hour or 24-hour format on the publish screen.
* **Time Increments**: you can choose to have the time options increment by 1 minute, 5 minutes, 15 minutes, 30 minutes, or 1 hour.

## Template Tags

When displaying your field within your templates, you can format the output using [standard EE date formatting tokens](http://expressionengine.com/user_guide/templates/date_variable_formatting.html). If you don't format your field output, the time's value in seconds will be displayed.

Example:

`{my_time_field format="%g:%i%a"}`

This yields **7:00pm**.

When using as a Low Variables var, you'll ned to use low Variables' `parse` tag:

`{exp:low_variables:parse var="lv_my_var" format="%g:%i%a"}`

*Time Select requires ExpressionEngine 3.0.0 or greater, or ExpressionEngine 2.1.3 or greater, and is compatible with Matrix, Grid, Blocks/Bloqs, and Low Variables fields.*