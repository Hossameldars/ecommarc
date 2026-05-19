{{-- resources/views/emails/reset-password.blade.php --}}
<h2>إعادة تعيين كلمة المرور</h2>
<p>اضغط على الرابط لإعادة تعيين كلمة المرور:</p>
<a href="{{ config('app.frontend_url') }}/reset-password?token={{ $token }}">
    إعادة تعيين كلمة المرور
</a>
<p>الرابط صالح لمدة 60 دقيقة فقط.</p>