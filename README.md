Gravity-HTTP-POST-Plugin
========================

Simple plugin for Gravity Forms that allows you to send an HTTP POST request on form submission, and map fields to the target URL.

Note: Requires [Gravity Forms](http://www.gravityforms.com/)

Install and activate plugin.

New menu will appear under each form's settings.

Enter the target URL, set Request Type to "POST Request" (I'll add GET at some point) and check "Active?"

Each form field will appear under the Mapping header - enter the target parameter. Multi-field inputs can either be mapped to individual fields or serialized to a single output.

Should work on most fields except File Upload (haven't looked at that).


Pretty simple - works for my purpose but may have bugs if pushed.

Email me at chris [at] chrisgoddard.me for questions/comments or fork and improve!
