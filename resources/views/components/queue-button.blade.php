<button {{ $attributes->merge(['name' => 'queueButton', 
    'class' => 'queueButton',
    'data-id' =>  $songid]) }}>
    
    {{ $slot }}
</button>
