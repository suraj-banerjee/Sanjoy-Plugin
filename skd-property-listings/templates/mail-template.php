<div>
    <table border="0" cellpadding="0" cellspacing="0" width="600" id="m_1843499424794142306template_container"
        style="background-color:#ffffff;border:1px solid #dedede;border-radius:3px">
        <tbody>
            <tr>
                <td align="center" valign="top">

                    <table border="0" cellpadding="0" cellspacing="0" width="600" id="m_1843499424794142306template_header"
                        style="background-color:#91d408;color:#ffffff;border-bottom:0;font-weight:bold;line-height:100%;vertical-align:middle;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;border-radius:3px 3px 0 0">
                        <tbody>
                            <tr>
                                <td id="m_1843499424794142306header_wrapper" style="padding:36px 48px;display:block">
                                    <h1
                                        style="font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;font-size:30px;font-weight:300;line-height:150%;margin:0;text-align:left;color:#ffffff">
                                        [<span class="il"><?php echo get_bloginfo('name'); ?></span>] : <?php echo $mailTitleTxt; ?></h1>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </td>
            </tr>
            <tr>
                <td align="center" valign="top">

                    <table border="0" cellpadding="0" cellspacing="0" width="600" id="m_1843499424794142306template_body">
                        <tbody>
                            <tr>
                                <td valign="top" id="m_1843499424794142306body_content" style="background-color:#ffffff">

                                    <table border="0" cellpadding="20" cellspacing="0" width="100%">
                                        <tbody>
                                            <tr>
                                                <td valign="top" style="padding:48px 48px 32px">
                                                    <div id="m_1843499424794142306body_content_inner"
                                                        style="color:#636363;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;font-size:14px;line-height:150%;text-align:left">
                                                        <img src="<?php echo esc_url($mailSite_icon_url); ?>"
                                                            alt="LOGO" width="80" height="50"
                                                            style="margin:auto;display:table" class="CToWUd"
                                                            data-bit="iit"><br>
                                                        Dear <?php echo $mailContactName; ?>,<br>

                                                        <?php echo $emailBodyHtml; ?>

                                                        The Administrators of <span class="il"><?php echo get_bloginfo('name'); ?></span><br>
                                                        <br>
                                                        <b style="margin:auto;display:table"><span class="il"><?php echo get_bloginfo('name'); ?></span>
                                                            Pty Ltd ABN: 94679712531<b>
                                                            </b></b>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                </td>
                            </tr>
                        </tbody>
                    </table>

                </td>
            </tr>
        </tbody>
    </table>
</div>