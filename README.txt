SDR IMS Dark Interface - PHP + MySQL

This version uses the dark SDR IMS interface and includes billing, email welcome message code, and low stock minimum 10 alert.

Login:
Username: admin
Password: admin123

Other demo users:
manager / admin123
staff1 / admin123

How to run with XAMPP:
1. Start Apache and MySQL.
2. Copy folder ims_pro_dark_php_mysql to:
   C:/xampp/htdocs/
3. Open phpMyAdmin:
   http://localhost/phpmyadmin
4. Import database.sql.
5. Open:
   http://localhost/ims_pro_dark_php_mysql/

Important:
- This version uses a fresh database name: sdr_ims
- This prevents old password-hash problems.
- If password becomes invalid, open:
  http://localhost/ims_pro_dark_php_mysql/reset_admin.php
  Then delete reset_admin.php after successful login.

Main modules:
- Login
- Dashboard with dark cards and charts
- Products
- Categories
- Suppliers
- Customers
- Stock movements
- Purchase / stock in
- Sale / stock out
- Reports
- Low stock alerts
- User management


New updates added:
- Name changed to SDR IMS
- Footer: Developed by Samiru Dinushan
- Auto-generated attractive bill after every sale
- New bill.php print bill page
- Welcome email after creating a user account: subject/message "Welcome to SDR IMS"
- Low stock minimum quantity is 10

Email note:
PHP mail() must be configured on your server/hosting. Local XAMPP/Laragon may not send email unless SMTP/sendmail is configured.


Included branding asset:
- assets/img/sdr-ims-logo.png (uploaded SDR IMS logo)


New feature update:
- Developer Contact page added.
  Contact No: 0719957871
  E-mail: s.d.randiwela@gmail.com
- Users can upload their own profile picture from My Profile.
- Admin can add a profile picture when creating user accounts.
- Sales form includes discount amount.
- Bill shows subtotal, discount and grand total.
- Product form includes barcode field.
- Sales form includes barcode reader input.
  Use a USB/Bluetooth barcode scanner, or type barcode and press Enter.
- Generated bill is optimized for A5 print size.
- Login button has stylish motion animation.

Important database note:
Re-import database.sql after replacing the project because new columns were added:
users.profile_picture
products.barcode
sales.subtotal
sales.discount


Bug fix update:
- Fixed profile.php showing raw PHP code on screen.
- Reduced Developer Contact page logo/image size.
- Added update_existing_database.sql for existing database updates.

For an existing database, import update_existing_database.sql in phpMyAdmin.
For a fresh install, import database.sql only.


Final design update:
- Footer now includes developer contact details without image:
  Developed by Samiru Dinushan | 0719957871 | s.d.randiwela@gmail.com
- Developer Contact page no longer shows the logo image.
- Login page redesigned with modern attractive glass UI and animated motion button.
- Bill print page size set to exact A5 millimeter size: 148mm × 210mm.


Profile page fix:
- profile.php no longer shows PHP warnings when the old session user_id is invalid.
- If you already imported an older database, import update_existing_database.sql once.
- After importing database changes, logout and login again.


Footer update:
- Footer now shows only: Developed by Samiru Dinushan
- Footer appears only after login because it is included in authenticated pages through partials/footer.php.


Login page update:
- Removed login page contact information.
- Added password show/hide eye icon.


Password login update:
- Password show/hide icon fixed and moved to the right side of password input.
- Added Forgot Password option on login page.
- New forgot_password.php lets users change password using username + email.


Login text update:
- Removed the word A5 from the login page only.
- Bill print size remains 148mm × 210mm.


Login page text update:
- Removed bill/printing/bill-size wording from the login page.
- Bill print size is still configured inside the bill page only.


Forgot-password page fix:
- Login/forgot-password pages now scroll correctly on small screens.
- Password eye icons are forced to the right side of the password fields.


Final password icon fix:
- Replaced the Font Awesome password eye with a stable built-in eye button.
- Forgot password page scrolling forced on all screen sizes.


Final scroll/left-eye update:
- Login and forgot-password pages now use normal document scrolling.
- Login page password show icon moved to the left side.


Profile layout update:
- My Profile page cards now show vertically one by one down.


Direct profile layout fix:
- profile.php now has direct inline vertical layout.
- CSS cache buster added: style.css?v=profile_stack_2.
- If it still shows old layout, press Ctrl + F5 or clear browser cache.


Validation update:
- All phone number fields are limited to maximum 10 numeric characters.
- Password rule applied to user creation, profile password change, and forgot password:
  5-8 characters, must contain letters, numbers, and symbols.


Bill update:
- Bill print CSS changed from fixed A5 to flexible page size, so it fits any print paper/page.
- Sales now store issued_by user name.
- Bill Issued By section displays the SDR IMS user who issued the bill instead of developer name.
- For old databases, import update_existing_database.sql once.


Professional bill design update:
- bill.php redesigned as an attractive printable invoice.
- Includes logo, invoice number, customer details, issued-by user, item table, totals, signatures, and clean print CSS.
- Print layout is optimized to fit any printer/page size.


Bill image fix:
- Removed the large logo image from bill.php.
- Added a small text mark instead of image.
- Added CSS cache buster: style.css?v=bill_no_image_1.
- If the old logo still appears, press Ctrl + F5.


Bill blank area fix:
- Removed pro-bill-watermark from bill.php.
- Added CSS to remove bill top blank area.
- Added CSS cache buster: style.css?v=bill_blank_removed_1.


Bill logo update:
- Added uploaded SDR IMS logo to the bill header.
- Replaced the small SDR text box with the logo.
- Added CSS cache buster: style.css?v=bill_logo_added_1.


Add User password field update:
- Added show/hide password button to the Users → Add User password field.
- Added CSS cache buster: style.css?v=add_user_password_eye_1.


Password security update:
- All password fields block copy, cut, paste, drop and right-click.
- Ctrl+C, Ctrl+X, Ctrl+V and Shift+Insert are blocked inside password fields.
- Browser/OS screenshots cannot be fully blocked by a normal website.
- Best-effort screenshot protection added: passwords hide again when Print Screen is pressed or the tab loses focus.


Dark / Light Mode Update:
- Added stylish animated dark/light mode toggle.
- Theme preference is saved in the browser using localStorage.
- Toggle added to topbar after login.
- Floating toggle added to login and forgot password pages.
- Added assets/js/theme-toggle.js and CSS variables for light mode.


Fixed Topbar Update:
- Top dashboard bar now stays fixed at the top while scrolling.
- Page content starts below the topbar.
- Works on desktop and mobile.
- CSS cache buster updated to style.css?v=fixed_topbar_1.


ONLINE IMS LIGHT / USER / LOW STOCK UPDATE:
- Fixed light mode visibility for fields, tables, cards, labels, buttons and details.
- Added duplicate username validation in users.php.
- Added duplicate email validation in users.php.
- Added popup messages:
  This username already exsists
  This email already exsists
- Added dashboard low stock banner.
- Added stylish low stock popup alert on dashboard.
- CSS cache buster updated to style.css?v=online_ims_light_user_lowstock_1.
