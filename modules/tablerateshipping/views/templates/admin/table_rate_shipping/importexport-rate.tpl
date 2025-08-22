{**
 * Overrides carrier shipping with Table Rate Shipping
 *
 * Table Rate Shipping by Kahanit(https://www.kahanit.com/) is licensed under a
 * Creative Creative Commons Attribution-NoDerivatives 4.0 International License.
 * Based on a work at https://www.kahanit.com/.
 * Permissions beyond the scope of this license may be available at https://www.kahanit.com/.
 * To view a copy of this license, visit http://creativecommons.org/licenses/by-nd/4.0/.
 *
 * @author    Amit Sidhpura <amit@kahanit.com>
 * @copyright 2016 Kahanit
 * @license   http://creativecommons.org/licenses/by-nd/4.0/
 *}

<div class="modal fade bs-example-modal-lg" id="modalimportexportcsv" role="dialog"
     aria-labelledby="modalimportexportcsvlabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">{l s='Close' mod='tablerateshipping'}</span>
                </button>
                <h4 class="modal-title" id="modalimportexportcsvlabel"></h4>
            </div>
            <form id="trs-importexportcsv-form" class="form-horizontal" enctype="multipart/form-data">
                <div class="panel">
                    <div class="panel-body">
                        <div id="csv-file-form-group" class="form-group">
                            <label for="csv_file" class="col-lg-3 control-label">
                                {l s='Select CSV file' mod='tablerateshipping'}
                            </label>

                            <div class="col-lg-9">
                                <div id="fileupload">
                                    <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
                                    <div class="row fileupload-buttonbar">
                                        <div class="col-lg-8">
                                            <!-- The fileinput-button span is used to style the file input field as button -->
                                            <div class="btn-group">
                                                <span class="btn btn-default fileinput-button">
                                                    <i class="glyphicon glyphicon-plus"></i>
                                                    <span>{l s='Add files' mod='tablerateshipping'}</span>
                                                    <input type="file" name="files[]" multiple>
                                                </span>
                                                <button type="submit" class="btn btn-default start">
                                                    <i class="glyphicon glyphicon-upload"></i>
                                                    <span>{l s='Start upload' mod='tablerateshipping'}</span>
                                                </button>
                                                <button type="reset" class="btn btn-default cancel">
                                                    <i class="glyphicon glyphicon-ban-circle"></i>
                                                    <span>{l s='Cancel upload' mod='tablerateshipping'}</span>
                                                </button>
                                                <button type="button" class="btn btn-danger delete">
                                                    <i class="glyphicon glyphicon-trash"></i>
                                                    <span>{l s='Delete' mod='tablerateshipping'}</span>
                                                </button>
                                            </div>
                                            &nbsp;<input type="checkbox" class="toggle">
                                            <!-- The global file processing state -->
                                            <span class="fileupload-process"></span>
                                        </div>
                                        <!-- The global progress state -->
                                        <div class="col-lg-4 fileupload-progress fade">
                                            <!-- The global progress bar -->
                                            <div class="progress progress-striped active" role="progressbar"
                                                 aria-valuemin="0"
                                                 aria-valuemax="100">
                                                <div class="progress-bar progress-bar-success" style="width:0;"></div>
                                            </div>
                                            <!-- The extended global progress state -->
                                            <div class="progress-extended">&nbsp;</div>
                                        </div>
                                    </div>
                                    <!-- The table listing the files available for upload/download -->
                                    <table role="presentation" class="table table-striped">
                                        <thead>
                                        <tr>
                                            <th class="trs-uploaddownload-file-column">{l s='File' mod='tablerateshipping'}</th>
                                            <th class="trs-uploaddownload-size-column">{l s='Size' mod='tablerateshipping'}</th>
                                            <th class="trs-uploaddownload-actions-column">{l s='Actions' mod='tablerateshipping'}</th>
                                        </tr>
                                        </thead>
                                        <tbody class="files"></tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                        <div id="csv-separator-form-group" class="form-group">
                            <label for="csv_separator" class="col-lg-3 control-label">
                                {l s='CSV separator' mod='tablerateshipping'}
                            </label>

                            <div class="col-lg-9">
                                <input type="text" name="csv_separator" class="form-control" id="csv_separator" size="3" value=","/>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <input type="hidden" id="trs-importexportcsv-action" name="trs-importexportcsv-action" value=""/>
                        <button type="button" class="btn btn-default pull-right trs-importexportcsv-form-submit">
                            <i class="glyphicon glyphicon-floppy-saved"></i> {l s='Save' mod='tablerateshipping'}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>