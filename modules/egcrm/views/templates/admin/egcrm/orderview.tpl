





<div class="row">
    <div class="col-lg-7">
        <div class="panel">
            <div class="panel-heading">
                <i class="icon-credit-card"></i>
                {l s='Order'}
                <span class="badge">{$order->id}</span>
                <span class="badge">{l s="#"}{$order->seqn}</span>
                <div class="panel-heading-action">
                    <div class="btn-group">

                    </div>
                </div>
            </div>

            <!-- Tab nav -->
            <ul class="nav nav-tabs" id="tabOrder">

                <li class="active">
                    <a href="#status">
                        <i class="icon-time"></i>
                        {l s='Order detail'}
                    </a>
                </li>
                <li>
                    <a href="#documents">
                        <i class="icon-file-text"></i>
                        {l s='Changes order'}
                    </a>
                </li>
            </ul>
            <!-- Tab content -->
            <div class="tab-content panel">
                <!-- Tab status -->
                <div class="tab-pane active" id="status">
                    <div id="userinfo" class="panel">
                        <div class="panel-heading">
                            <i class="icon-user"></i> {l s='User info'}
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="control-label col-lg-12">{l s='User name'}</label>
                                <div class="col-lg-6 ">
                                    <input type="text" name="bayer_name" value="{$order->bayer_name}" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-lg-3">{l s='Phone'}</label>
                                <div class="col-lg-6 ">
                                    <input type="text" name="phone" value="{$order->phone}" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-lg-3">{l s='Phone 2'}</label>
                                <div class="col-lg-6 ">
                                    <input type="text" name="phone2" value="{$order->phone2}" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-lg-3">{l s='city'}</label>
                                <div class="col-lg-6 ">
                                    <input type="text" name="city" value="{$order->city}" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-lg-3">{l s='Address'}</label>
                                <div class="col-lg-6 ">
                                    <input type="text" name="address" value="{$order->address}" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-lg-3">{l s='email'}</label>
                                <div class="col-lg-6 ">
                                    <input type="text" name="email" value="{$order->email}" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--div id="address" class="panel">
                        <div class="panel-heading">
                            <i class="icon-truck "></i> {l s='Shipping'}
                        </div>
                        hello
                    </div -->
                </div>
                <!-- Tab documents -->
                <div class="tab-pane" id="documents">

                </div>
            </div>
            <script>
                $('#tabOrder a').click(function (e) {
                    e.preventDefault()
                    $(this).tab('show')
                })
            </script>
            <hr />
            <!-- Tab nav -->
            <ul class="nav nav-tabs" id="myTab">

                <li class="active">
                    <a href="#shipping">
                        <i class="icon-truck "></i>
                        {l s='Shipping'} <span class="badge">222</span>
                    </a>
                </li>
                <li>
                    <a href="#returns">
                        <i class="icon-undo"></i>
                        {l s='Merchandise Returns'} <span class="badge">222</span>
                    </a>
                </li>
            </ul>
            <!-- Tab content -->
            <div class="tab-content panel">
                <!-- Tab shipping -->
                <div class="tab-pane active" id="shipping">
                    <h4 class="visible-print">{l s='Shipping'} <span class="badge">222</span></h4>

                </div>
                <!-- Tab returns -->
                <div class="tab-pane" id="returns">
                    <h4 class="visible-print">{l s='Merchandise Returns'} <span class="badge">333</span></h4>

                </div>
            </div>
            <script>
                $('#myTab a').click(function (e) {
                    e.preventDefault()
                    $(this).tab('show')
                })
            </script>
        </div>

    </div>

</div>


