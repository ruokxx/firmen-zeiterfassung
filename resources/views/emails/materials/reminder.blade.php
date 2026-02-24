@component('mail::message')
# Erinnerung: Material entnommen?

Hallo {{ $user->first_name }},

hast du heute Material aus dem Lager entnommen? Falls ja, trage dieses bitte noch schnell im System ein. 
Dies hilft uns, den Überblick über die Bestände zu behalten und rechtzeitig neues Material zu bestellen.

@component('mail::button', ['url' => route('materials.index')])
Zum Lager
@endcomponent

Danke für deine Mithilfe!

<br>
<small>
    Diese Erinnerung kannst du jederzeit in deinen <a href="{{ route('profile.edit') }}">Profil-Einstellungen</a> deaktivieren.
</small>
@endcomponent
