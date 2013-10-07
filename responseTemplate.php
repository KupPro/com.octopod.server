<octopod sessionId="<?= Response::get('sessionId') ?>"
         debug="<?= (Response::get('debug')) ? "true": "false" ?>"
         orientation="<?= Response::get('orientation') ?>"
         cacheImagesCounter="<?= Response::get('cacheImagesCounter') ?>"
         cacheMarkupCounter="<?= Response::get('cacheMarkupCounter') ?>"
         installationId="<?= Response::get('installationId') ?>">

    <?php //========Views=========?>
    <?php if (sizeof(Response::get('views'))): ?>
        <views defaultViewId="<?= Config::get('default.view') ?>">
            <?php foreach (Response::get('views') as $view): ?>
                <view cache="<?= ($view->cached()) ? "true": "false" ?>" id="<?= $view->id() ?>">
                    <![CDATA[
                    <?= $view->render(); ?>
                    ]]>
                </view>
            <?php endforeach; ?>
        </views>
    <?php endif; ?>

    <?php //========Resources=========?>
    <?php if (sizeof(Response::get('resources'))): ?>
        <resources>
            <?php foreach (Response::get('resources') as $resource): ?>
                <file name="<?= $resource['filename'] ?>" url="<?= $resource['url'] ?>"/>
            <?php endforeach; ?>
        </resources>
    <?php endif; ?>


    <?php //========System events========= ?>
    <?php if (!is_null(Response::get('systemEvents'))): ?>
        <systemEvents>
            <![CDATA[
            <systemEventsContent>
                <?= Response::get('systemEvents')->render(); ?>
            </systemEventsContent>
            ]]>
        </systemEvents>
    <?php endif; ?>



    <?php //========Actions=========?>
    <actions>
        <?php if (!is_null(Response::get('badgeCounter'))): ?>
            <action_setBadge counter="<?= Response::get('badgeCounter') ?>"/>
        <?php endif; ?>

        <?php if (sizeof(Response::get('variables'))): ?>
            <?php foreach (Response::get('variables') as $var): ?>
                <action_setVariable key="<?= $var['name'] ?>" value="<?= $var['value'] ?>"/>
            <?php endforeach; ?>

        <?php endif; ?>


        <?php if (sizeof(Response::get('settings'))): ?>
            <?php foreach (Response::get('settings') as $set => $val): ?>
                <action_setSetting key="<?= $set ?>" value="<?= $val ?>"/>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!is_null(Response::get('actions'))): ?>
            <?= Response::get('actions')->render(); ?>
        <?php endif; ?>
    </actions>


    <?php //========Response parameters=========?>

    <?php if (sizeof(Response::get('responseParameters'))): ?>
        <parameters>
            <?php foreach (Response::get('responseParameters') as $key => $value): ?>
                <parameter key="<?= $key ?>" value="<?= $value ?>"/>
            <?php endforeach; ?>
        </parameters>
    <?php endif; ?>

    <?php //========SQLITE dump=========?>

    <?php if ((Response::get('queries'))!=""): ?>
        <data>
            <![CDATA[
            <?=Response::get('queries')?>
            ]]>
        </data>
    <?php endif; ?>


    <?php //========Scripts file=========?>
    <?php if (!is_null(Response::get('scripts'))): ?>
        <?= Response::get('scripts')->render(); ?>
    <?php endif; ?>

</octopod>