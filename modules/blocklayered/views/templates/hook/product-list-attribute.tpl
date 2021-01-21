attribute1 - {$pa}
{foreach from=$my_product item='value'}
    <li>{$value.id_attribute} - {$value.name}</li>
{/foreach}