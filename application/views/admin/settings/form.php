<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-title">
        <?php echo lang('admin title setings'); ?>
      </div>
      <div class="card-body">
        <?php echo form_open('', array('role'=>'form')); ?>

    <?php foreach ($settings as $setting) { ?>
            
        <?php
        
        if ($setting['id'] != 33 && $setting['id'] != 31 && $setting['id'] != 32 && $setting['id'] != 30 && $setting['id'] != 29 && $setting['id'] != 28 && $setting['id'] != 27 && $setting['id'] != 26 && $setting['id'] != 25 && $setting['id'] != 24 && $setting['id'] != 23 && $setting['id'] != 22 && $setting['id'] != 21 && $setting['id'] != 20  ) {  

        // prepare field settings
        $field_data = array();

        if ($setting['is_numeric'])
        {
            $field_data['type'] = "number";
            $field_data['step'] = "any";
        }

        if ($setting['options'])
        {
            $field_options = array();
            if ($setting['input_type'] == "dropdown")
            {
                $field_options[''] = lang('admin input select');
            }
            $lines = explode("\n", $setting['options']);
            foreach ($lines as $line)
            {
                $option = explode("|", $line);
                $field_options[$option[0]] = $option[1];
            }
        }

        switch ($setting['input_size'])
        {
            case "small":
                $col_size = "col-sm-3";
                break;
            case "medium":
                $col_size = "col-sm-6";
                break;
            case "large":
                $col_size = "col-sm-9";
                break;
            default:
                $col_size = "col-sm-6";
        }

        if ($setting['input_type'] == 'textarea')
        {
            $col_size = "col-sm-12";
        }
        ?>

        <?php if ($setting['translate'] && $this->session->languages && $this->session->language) : ?>

            <?php // has translations ?>
            <?php
            $setting['value'] = (@unserialize($setting['value']) !== FALSE) ? unserialize($setting['value']) : $setting['value'];
            if ( ! is_array($setting['value']))
            {
                $old_value = $setting['value'];
                $setting['value'] = array();
                foreach ($this->session->languages as $language_key=>$language_name)
                {
                    $setting['value'][$language_key] = ($language_key == $this->session->language) ? $old_value : "";
                }
            }
            ?>

                <div class="form-group <?php echo $col_size; ?><?php echo form_error($setting['name']) ? ' has-error' : ''; ?>">
                    <?php echo form_label($setting['label'], $setting['name'], array('class'=>'control-label')); ?>
                    <?php if (strpos($setting['validation'], 'required') !== FALSE) : ?>
                        <span class="required">*</span>
                    <?php endif; ?>
                    <div role="tabpanel">
                        <ul class="nav nav-tabs" role="tablist">
                            <?php foreach ($this->session->languages as $language_key=>$language_name) : ?>
                                <li role="presentation" class="<?php echo ($language_key == $this->session->language) ? 'active' : ''; ?>"><a href="#<?php echo $language_key; ?>" aria-controls="<?php echo $language_key; ?>" role="tab" data-toggle="tab"><?php echo $language_name; ?></a></li>
                            <?php endforeach; ?>
                        </ul>

                        <div class="tab-content">
                            <?php foreach ($this->session->languages as $language_key=>$language_name) : ?>
                                <div role="tabpanel" class="tab-pane<?php echo ($language_key == $this->session->language) ? ' active' : ''; ?>" id="<?php echo $language_key; ?>">
                                    <br />
                                    <?php
                                    $field_data['name']  = $setting['name'] . "[" . $language_key . "]";
                                    $field_data['id']    = $setting['name'] . "-" . $language_key;
                                    $field_data['class'] = "form-control form-control-sm" . (($setting['show_editor']) ? " editor" : "");
                                    $field_data['value'] = (@$setting['value'][$language_key]) ? $setting['value'][$language_key] : "";

                                    // render the correct input method
                                    if ($setting['input_type'] == 'input')
                                    {
                                        echo form_input($field_data);
                                    }
                                    
                                    elseif ($setting['input_type'] == 'textarea')
                                    {
                                        echo form_textarea($field_data);
                                    }
                                    elseif ($setting['input_type'] == 'radio')
                                    {
                                        echo "<br />";
                                        foreach ($field_options as $value=>$label)
                                        {
                                            echo form_radio(array('name'=>$field_data['name'], 'id'=>$field_data['id'] . "-" . $value, 'value'=>$value, 'checked'=>(($value == $field_data['value']) ? 'checked' : FALSE)));
                                            echo $label;
                                        }
                                    }
                                    elseif ($setting['input_type'] == 'dropdown')
                                    {
                                        echo form_dropdown($setting['name'], $field_options, $field_data['value'], 'id="' . $field_data['id'] . '" class="' . $field_data['class'] . '"');
                                    }
                                    elseif ($setting['input_type'] == 'timezones')
                                    {
                                        echo "<br />";
                                        echo timezone_menu($field_data['value']);
                                    }
                                    ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <?php if ($setting['help_text']) : ?>
                        <span class="help-block"><?php echo $setting['help_text']; ?></span>
                    <?php endif; ?>
                </div>

        <?php else : ?>

            <?php // no translations
            $field_data['name']  = $setting['name'];
            $field_data['id']    = $setting['name'];
            $field_data['class'] = "form-control form-control-sm" . (($setting['show_editor']) ? " editor" : "");
            $field_data['value'] = $setting['value'];
                       
            $field_data2['name']  = $setting['name'];
            $field_data2['id']    = $setting['name'];
            $field_data2['class'] = "form-control form-control-sm" . (($setting['show_editor']) ? " editor" : "");
            $field_data2['value'] = $setting['value'];

            ?>
    
                <div class="form-group <?php echo $col_size; ?><?php echo form_error($setting['name']) ? ' has-error' : ''; ?>">
                    <?php echo form_label($setting['label'], $setting['name'], array('class'=>'control-label')); ?>
                    <?php if (strpos($setting['validation'], 'required') !== FALSE) : ?>
                        <span class="required">*</span>
                    <?php endif; ?>

                    <?php // render the correct input method
                     if ($setting['input_type'] == 'file')
                    {
                        echo form_upload($field_data2);
                    }
                    if ($setting['input_type'] == 'input')
                    {
                        echo form_input($field_data);
                    }
                    elseif ($setting['input_type'] == 'textarea')
                    {
                        echo form_textarea($field_data);
                    }
                    elseif ($setting['input_type'] == 'radio')
                    {
                        echo "<br />";
                        foreach ($field_options as $value=>$label)
                        {
                            echo form_radio(array('name'=>$field_data['name'], 'id'=>$field_data['id'] . "-" . $value, 'value'=>$value, 'checked'=>(($value == $field_data['value']) ? 'checked' : FALSE)));
                            echo $label;
                        }
                    }
                    elseif ($setting['input_type'] == 'dropdown')
                    {
                        echo form_dropdown($setting['name'], $field_options, $field_data['value'], 'id="' . $field_data['id'] . '" class="' . $field_data['class'] . '"');
                    }
                    elseif ($setting['input_type'] == 'timezones')
                    {
                        echo "<br />";
                        echo timezone_menu($field_data['value'],  'form-control form-control-sm"' . $field_data['id'] . '" class="' . $field_data['class'] . '"');
                    }
                    ?>

                    <?php if ($setting['help_text']) : ?>
                        <span class="help-block"><?php echo $setting['help_text']; ?></span>
                    <?php endif; ?>
                </div>
          

                    
      

        <?php endif; ?>

    <?php 
      }
      
      if ($setting['id'] == 33) { ?>
        
        <div class="form-group col-sm-6">
            <label for="user_card_section" class="control-label" style="display: block;">Show Card section</label>
          <select class="form-control" name="user_card_section" id="user_card_section" style="width:150px !important;display:inline-block;">
              <option value="0" <?Php if ($setting['value'] == 0) { print("selected"); } ?>>Disabled</option>            
              <option value="1" <?Php if ($setting['value'] == 1) { print("selected"); } ?>>Enabled</option>
          </select>
        </div>
        
      <?php }
      
    } ?>
          
          
      </div>
      <div class="card-footer-padding">
        <button type="submit"  class="btn btn-success btn-sm"><?php echo lang('core button save'); ?></button>
      </div>
    </div>
  </div>
</div>

<?php echo form_close(); ?>