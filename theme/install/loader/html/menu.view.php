            {if $isAdded}
            <div class="alert alert-success">
                <a href="#" class="close" data-dismiss="alert">&times;</a>
                <h4>Felicidades!</h4>
                <p>La frase fue agregada correctamente.</p>
            </div>
            {/if}
            <div id="addSetting">
                <form method="post" action="" class="form-horizontal">
                    <fieldset>
                        <legend>Agregar menú</legend>
                        <div class="control-group">
                            <div class="control-label">
                                <label>module_id</label>
                            </div>
                            <div class="controls">
                                {form_select name='menu[module_id]' options=$modules onchange='$(\'#phrase_module_id\').val(this.value);'}
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <label>section_id</label>
                            </div>
                            <div class="controls">
                                <select name="menu[section_id]">
                                    <option></option>
                                    <optgroup label="Bloque">
                                        {foreach from=$menus.block item=menu}
                                        <option value="{$menu}">{$menu}</option>
                                        {/foreach}
                                    </optgroup>
                                    <optgroup label="Menú padre">
                                        {foreach from=$menus.parent item=menu}
                                        <option value="{$menu.menu_id}">{$menu.var_name}</option>
                                        {/foreach}
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <label>var_name</label>
                            </div>
                            <div class="controls">
                                {form_input name='menu[var_name]' class='phrase_menu' id='menu'}
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <label>url_value</label>
                            </div>
                            <div class="controls">
                                {form_input name='menu[url_value]'}
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <label>ordering</label>
                            </div>
                            <div class="controls">
                                {form_input name='menu[ordering]' value='1'}
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <label>is_active</label>
                            </div>
                            <div class="controls">
                                <label class="radio inline">
                                    {form_radio name='menu[is_active]' value='1' checked='true'}
                                    Si
                                </label>
                                <label class="radio inline">
                                    {form_radio name='menu[is_active]' value='0'}
                                    No
                                </label>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend>Frase del ajuste</legend>
                        <div class="control-group">
                            <div class="control-label">
                                <label>language_id</label>
                            </div>
                            <div class="controls">
                                {form_input name='phrase[language_id]' value='es_MX'}
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <label>module_id</label>
                            </div>
                            <div class="controls">
                                {form_input name='phrase[module_id]' id='phrase_module_id' value='core'}
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <label>var_name</label>
                            </div>
                            <div class="controls">
                                {form_input name='phrase[var_name]' class='phrase_menu'}
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <label>text</label>
                            </div>
                            <div class="controls">
                                {form_input name='phrase[text_actual]' type='textarea'}
                            </div>
                        </div>
                    </fieldset>
                    <div class="control-group">
                        <div class="controls">
                            <input type="submit" class="btn btn-success" value="Guardar" />
                        </div>
                    </div>
                </form>
            </div>