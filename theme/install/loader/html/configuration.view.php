            {if $isAdded}
            <div class="alert alert-success">
                <a href="#" class="close" data-dismiss="alert">&times;</a>
                <h4>Felicidades!</h4>
                <p>La configuración fue agregada correctamente.</p>
            </div>
            {/if}
            <h3>Agregar configuración <div class="pull-right"><a href="#" class="btn btn-small" onclick="showDiv('addSetting'); showDiv('addGroup'); return false">Agregar ajuste</a> <a href="#" class="btn btn-small" onclick="showDiv('addGroup'); showDiv('addSetting'); return false">Agregar grupo</a></div></h3>
            <hr class="hr-condensed" />
            <div id="addSetting">
                <form method="post" action="" class="form-horizontal">
                    <fieldset>
                        <legend>Ajuste</legend>
                        <div class="control-group">
                            <div class="control-label">
                                <label>group_id</label>
                            </div>
                            <div class="controls">
                                {form_select name='setting[group_id]' options=$groups}
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <label>module_id</label>
                            </div>
                            <div class="controls">
                                {form_select name='setting[module_id]' options=$modules onchange='$(\'#phrase_module_id\').val(this.value);'}
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <label>type_id</label>
                            </div>
                            <div class="controls">
                                {form_input name='setting[type_id]'}
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <label>var_name</label>
                            </div>
                            <div class="controls">
                                {form_input name='setting[var_name]' id='setting' class='phrase'}
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <label>value</label>
                            </div>
                            <div class="controls">
                                {form_input name='setting[value_actual]'}
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <label>phrase_var_name</label>
                            </div>
                            <div class="controls">
                                {form_input name='setting[phrase_var_name]' class='phrase_setting'}
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <label>is_hidden</label>
                            </div>
                            <div class="controls">
                                <label class="radio inline">
                                    {form_radio name='setting[is_hidden]' value='1'}
                                    Si
                                </label>
                                <label class="radio inline">
                                    {form_radio name='setting[is_hidden]' value='0' checked='true'}
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
                                {form_input name='phrase[var_name]' class='phrase_setting'}
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
            <div id="addGroup" class="hide">
                <form method="post" action="" class="form-horizontal">
                    <fieldset>
                        <legend>Grupo</legend>
                        <div class="control-group">
                            <div class="control-label">
                                <label>group_id</label>
                            </div>
                            <div class="controls">
                                {form_input name='group[group_id]' class='phrase_group' id='group_setting'}
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <label>module_id</label>
                            </div>
                            <div class="controls">
                                {form_select name='group[module_id]' options=$modules}
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <label>phrase_var_name</label>
                            </div>
                            <div class="controls">
                                {form_input name='group[phrase_var_name]' class='phrase_group_setting'}
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
                                {form_input name='phrase[var_name]' class='phrase_group_setting'}
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