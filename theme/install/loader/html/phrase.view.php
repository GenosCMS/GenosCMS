            {if $isAdded}
            <div class="alert alert-success">
                <a href="#" class="close" data-dismiss="alert">&times;</a>
                <h4>Felicidades!</h4>
                <p>La frase fue agregada correctamente.</p>
            </div>
            {/if}
            <h3>Agregar frase</h3>
            <hr class="hr-condensed" />
            <div id="addSetting">
                <form method="post" action="" class="form-horizontal">
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
                            {form_select name='phrase[module_id]' options=$modules}
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                            <label>var_name</label>
                        </div>
                        <div class="controls">
                            {form_input name='phrase[var_name]'}
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
                    <div class="control-group">
                        <div class="controls">
                            <input type="submit" class="btn btn-success" value="Guardar" />
                        </div>
                    </div>
                </form>
            </div>