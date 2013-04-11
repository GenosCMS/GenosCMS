            {if $isAdded}
            <div class="alert alert-success">
                <a href="#" class="close" data-dismiss="alert">&times;</a>
                <h4>Felicidades!</h4>
                <p>La ruta fue agregada correctamente.</p>
            </div>
            {/if}
            <div id="addSetting">
                <form method="post" action="" class="form-horizontal">
                    <fieldset>
                        <legend>Agregar ruta</legend>
                        <div class="control-group">
                            <div class="control-label">
                                <label>module_id</label>
                            </div>
                            <div class="controls">
                                {form_select name='route[module_id]' options=$modules id='module' onchange='$(\'.rewrite\').keyup();'}
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <label>url</label>
                            </div>
                            <div class="controls">
                                {form_input name='route[url]' class='rewrite' id='url'}
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <label>replacement</label>
                            </div>
                            <div class="controls">
                                {form_input name='route[replacement]' class='rewrite' id='replacement'}
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
                    </fieldset>
                    <div class="control-group">
                        <div class="controls">
                            <input type="submit" class="btn btn-success" value="Guardar" />
                        </div>
                    </div>
                </form>
            </div>
            <div class="alert">
                <h5>Ejemplo</h5>
                <p><span id="rewrite_url"></span> &raquo; <span id="rewrite_replacement"></span></p>
            </div>