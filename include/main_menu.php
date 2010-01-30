<div id="productsandservices" class="yuimenubar yuimenubarnav">
    <div class="bd">
        <ul class="first-of-type">
        	<!----- Start Menu One ----->
            <li class="yuimenubaritem first-of-type"><a class="yuimenubaritemlabel" href="#">Capital Acquisition</a>
            <div id="acquisition" class="yuimenu">
            <div class="bd">
            <ul>
                <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/CER/index.php">New Acquisition</a></li>
                <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/CER/list.php?action=my">My Acquisitions</a></li>
                <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/CER/list.php">All Acquisitions</a></li>
            </ul>
            </div>
            </div>      
            </li>
            <!----- End Menu One ----->
            <!----- Start Menu Two ----->
            <li class="yuimenubaritem"><a class="yuimenubaritemlabel" href="#">Requisition Request</a>
            <div id="requisition" class="yuimenu">
            <div class="bd">                    
            <ul>
                <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/PO/index.php">New Requisition</a></li>
                <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/PO/list.php?action=my&access=N">My Requisitions</a>
                <div id="myRequisitionFilter" class="yuimenu">
                    <div class="bd">
                        <ul class="first-of-type">
                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/PO/list.php?action=my&status=All">All</a></li>
                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/PO/list.php?action=my&status=N">New (default)</a></li>
                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/PO/list.php?action=my&status=A">Approved</a></li>
                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/PO/list.php?action=my&status=O">Vendor Kickoff</a></li>
                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/PO/list.php?action=my&status=X">Not Approved</a></li>
                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/PO/list.php?action=my&status=C">Canceled</a></li>
                        </ul>            
                    </div>
                </div></li>
                <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/PO/list.php">All Requisitions</a>
                <div id="requisitionFilter" class="yuimenu">
                    <div class="bd">
                        <ul class="first-of-type">
                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/PO/list.php?status=All">All</a></li>
                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/PO/list.php">New (default)</a></li>
                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/PO/list.php?status=A">Approved</a></li>
                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/PO/list.php?status=O">Vendor Kickoff</a></li>
                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/PO/list.php?status=X">Not Approved</a></li>
                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/PO/list.php?status=C">Canceled</a></li>
                        </ul>            
                    </div>
                </div></li>
                <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/PO/search.php">Search Requisitions</a></li>
                <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/PO/Reports/index.php">Requisitions Reports</a></li>
            </ul>
            </div>
            </div>
            </li>
            <!----- End Menu Two ----->
            <!----- Start Menu Three ----->
            <?php if ($_SESSION['request_access'] >= 1) { ?>
            <li class="yuimenubaritem"><a class="yuimenubaritemlabel" href="#">Administration</a>
            <div id="administration" class="yuimenu">
            <div class="bd">                    
            <ul>
            	<?php if ($_SESSION['request_access'] >= 2 AND array_key_exists('id', $_GET)) { ?>
                <li class="yuimenuitem"><a class="yuimenuitemlabel" href="#" id="adminPanelMenu">Modify Requisition</a></li>
                <?php } ?>            
            	<?php if ($_SESSION['request_access'] == 3) { ?>
                <li class="yuimenuitem"><a class="yuimenuitemlabel" href="#" id="historyPanelMenu">Requisition History</a></li>
                <?php } ?>
                <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/Administration/users.php">User Management</a></li>
                <li class="yuimenuitem"><a class="yuimenuitemlabel" href="#">Database</a>
                <div id="database" class="yuimenu">
                    <div class="bd">
                        <ul class="first-of-type">
                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/Administration/db/controller.php">Controllers</a></li>
                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/Administration/db/vendors.php">AS/400 Vendors</a></li>
                        </ul>            
                    </div>
                </div>                    
                </li>
                <li class="yuimenuitem"><a class="yuimenuitemlabel" href="#">Utilities</a>
                <div id="utilities" class="yuimenu">
                    <div class="bd">
                        <ul class="first-of-type">
                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/Administration/notify.php">Notify Users by Email</a></li>
                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/Administration/notify_web.php">Notify Users by Webs</a></li>
                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/Administration/testemail.php">Send Test Email</a></li>
                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/Administration/summary.php">Usage Summary</a></li>     
                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/Administration/comments.php">Comments</a></li>
                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/Administration/updateRSS.php">Update RSS</a></li>                                                   
                        </ul>            
                    </div>
                </div>                    
                </li>                
                <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?= $default['URL_HOME']; ?>/Administration/settings.php">Application Settings</a></li>
            </ul>                    
            </div>
            </div>                                        
            </li>
            <?php } ?>
            <!----- End Menu Three ----->     
        </ul>            
    </div>
</div>
<div id="messageCenter" <?= ($hotMessage) ? 'class="hotMessage"' : ''; ?> style="display:none"><div><?= $message; ?></div></div>
<div id='adminPanel' style='display:none'></div>
<div id='historyPanel' style='display:none'></div>
