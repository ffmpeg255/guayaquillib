<?php
include_once('config.php');

$options = @$_GET['options'];
$replacementtypes = @$_GET['replacementtypes'];

if (!$options) {
    $options = array('crosses');
}
if (!$replacementtypes) {
    $replacementtypes = array('Default');
}
?>
<form name="findByOEM" id="findByOEM" action="am_findoem.php" method="get">
    <div class="g_input">
        <label for="brand">Brand</label>
        <input name="brand" type="text" id="brand" size="20" style="width:200px;" value="<?php echo htmlspecialchars(trim($_GET['brand']));?>"/>
    </div>
    <div class="g_input">
        <label for="oem">OEM</label>
        <input name="oem" type="text" id="oem" size="20" style="width:200px;" value="<?php echo htmlspecialchars(trim($_GET['oem']));?>"/>
    </div>
    <div class="g_input">
        Search options: 
        <input name="options[]" value="crosses" id=crosses" type="checkbox" <?php echo in_array('crosses', $options) ? 'checked="cheched"' : '' ?>/>
        <label for="crosses">Crosses</label>

        <input name="options[]" value="weights" id=weights" type="checkbox" <?php echo in_array('weights', $options) ? 'checked="cheched"' : '' ?>/>
        <label for="weights">Weights</label>

        <input name="options[]" value="names" id=names" type="checkbox" <?php echo in_array('names', $options) ? 'checked="cheched"' : '' ?>/>
        <label for="crosses">Names</label>

        <input name="options[]" value="properties" id=properties" type="checkbox" <?php echo in_array('properties', $options) ? 'checked="cheched"' : '' ?>/>
        <label for="properties">Properties</label>

        <input name="options[]" value="images" id=images" type="checkbox" <?php echo in_array('images', $options) ? 'checked="cheched"' : '' ?>/>
        <label for="images">Images</label>
    </div>
    <div class="g_input">
        Replacement types: 
        <input name="replacementtypes[]" value="synonym" id=synonym" type="checkbox" <?php echo in_array('synonym', $replacementtypes) ? 'checked="cheched"' : '' ?>/>
        <label for="synonym">Synonym</label>

        <input name="replacementtypes[]" value="PartOfTheWhole" id=PartOfTheWhole" type="checkbox" <?php echo in_array('PartOfTheWhole', $replacementtypes) ? 'checked="cheched"' : '' ?>/>
        <label for="PartOfTheWhole">PartOfTheWhole</label>

        <input name="replacementtypes[]" value="Replacement" id=Replacement" type="checkbox" <?php echo in_array('Replacement', $replacementtypes) ? 'checked="cheched"' : '' ?>/>
        <label for="Replacement">Replacement</label>

        <input name="replacementtypes[]" value="Duplicate" id=Duplicate" type="checkbox" <?php echo in_array('Duplicate', $replacementtypes) ? 'checked="cheched"' : '' ?>/>
        <label for="Duplicate">Duplicate</label>

        <input name="replacementtypes[]" value="Tuning" id=Tuning" type="checkbox" <?php echo in_array('Tuning', $replacementtypes) ? 'checked="cheched"' : '' ?>/>
        <label for="Tuning">Tuning</label>

        <input name="replacementtypes[]" value="Bidirectional" id=Bidirectional" type="checkbox" <?php echo in_array('Bidirectional', $replacementtypes) ? 'checked="cheched"' : '' ?>/>
        <label for="Bidirectional">Bidirectional</label>
    </div>

    <input type="submit" name="oemSubmit" id="oemSubmit" />
</form>
