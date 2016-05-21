<style type="text/css">
    input {
        display: block;
    }
</style>
<?php
$counter = 0;
?>


<span>some text</span>

<form id="debug-form">
    <datalist name="<?php echo "input_{$counter}"; ?>" id="browsers">
        <option value="Internet Explorer">
        <option value="Firefox">
        <option value="Chrome">
        <option value="Opera">
        <option value="Safari">
    </datalist>
    <select name="<?php echo "input_{$counter}"; ?>">
        <option value="volvo">Volvo</option>
        <option value="saab">Saab</option>
        <option value="mercedes">Mercedes</option>
        <option value="audi">Audi</option>
    </select>
    <textarea name="<?php echo "input_{$counter}"; ?>" value="<?php echo "value_{$counter}";
++$counter; ?>"></textarea>
    <input type="button" name="<?php echo "input_{$counter}"; ?>" value="<?php echo "value_{$counter}";
++$counter; ?>">
    <input type="checkbox" name="<?php echo "input_{$counter}"; ?>" value="<?php echo "value_{$counter}";
++$counter; ?>">
    <input type="color" name="<?php echo "input_{$counter}"; ?>" value="#ffffff">
    <input type="date" name="<?php echo "input_{$counter}"; ?>" value="">
    <input type="datetime" name="<?php echo "input_{$counter}"; ?>" value="<?php echo "value_{$counter}";
++$counter; ?>">
    <input type="datetime-local" name="<?php echo "input_{$counter}"; ?>" value="<?php echo "value_{$counter}";
++$counter; ?>">
    <input type="email" name="<?php echo "input_{$counter}"; ?>" value="">
    <input type="file" name="<?php echo "input_{$counter}"; ?>" value="<?php echo "value_{$counter}";
++$counter; ?>">
    <input type="hidden" name="<?php echo "input_{$counter}"; ?>" value="<?php echo "value_{$counter}";
++$counter; ?>">

    <input type="image" name="<?php echo "input_{$counter}"; ?>" value="<?php echo "value_{$counter}";
++$counter; ?>">
    <input type="month" name="<?php echo "input_{$counter}"; ?>" value="<?php echo "value_{$counter}";
++$counter; ?>">
    <input type="password" name="<?php echo "input_{$counter}"; ?>" value="<?php echo "value_{$counter}";
++$counter; ?>">
    <input type="radio" name="<?php echo "input_{$counter}"; ?>" value="<?php echo "value_{$counter}";
++$counter; ?>">
    <input type="range" name="<?php echo "input_{$counter}"; ?>" value="<?php echo "value_{$counter}";
++$counter; ?>">
    <input type="reset" name="<?php echo "input_{$counter}"; ?>" value="<?php echo "value_{$counter}";
++$counter; ?>">
    <input type="submit" name="<?php echo "input_{$counter}"; ?>" value="<?php echo "value_{$counter}";
++$counter; ?>">

    <input type="tel" name="<?php echo "input_{$counter}"; ?>" value="<?php echo "value_{$counter}";
++$counter; ?>">
    <input type="text" name="<?php echo "input_{$counter}"; ?>" value="<?php echo "value_{$counter}";
++$counter; ?>">
    <input type="url" name="<?php echo "input_{$counter}"; ?>" value="<?php echo "value_{$counter}";
++$counter; ?>">
    <input type="week" name="<?php echo "input_{$counter}"; ?>" value="<?php echo "value_{$counter}";
++$counter; ?>">
</form>