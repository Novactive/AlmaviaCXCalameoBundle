<?php

/*
 * AlmaviaCXCalameoBundle Bundle.
 *
 * @author    AlmaviaCX
 * @copyright 2021 AlmaviaCX
 * @license   MIT Licence
 */

declare(strict_types=1);

namespace AlmaviaCX\Calameo\Tests\Unit\API;

use DateTime;
use AlmaviaCX\Calameo\API\Value\FolderList;
use AlmaviaCX\Calameo\API\Value\Response\Response;

class FolderListSerializeTest extends SerializerTest
{
    public function testFolderListResponse(): void
    {
        /** @var FolderList $folderList */
        $folderList = $this->testDeserializeResponse(
            '{
               "response": {
                  "status": "ok",
                  "version": 2,
                  "requestid": "EGNNi2",
                  "requests": 162,
                  "content": {
                     "total": 1,
                     "start": 0,
                     "step": 10,
                     "items": [
                        {
                           "ID": 4540112,
                           "AccountID": 4516825,
                           "Name": "Mon dossier",
                           "Description": "",
                           "Books": 0,
                           "Subscribers": 0,
                           "Creation": "2015-10-06 16:55:39",
                           "Modification": "2015-10-06 16:55:39",
                           "PublicUrl": "http://www.calameo.com/subscriptions/4540112"
                        }
                     ]
                  }
               }
            }',
            Response::TYPE_OK,
            FolderList::class
        );

        self::assertEquals(1, $folderList->total);
        self::assertEquals(0, $folderList->start);
        self::assertEquals(10, $folderList->step);

        self::assertEquals(4540112, $folderList->items[0]->id);
        self::assertEquals(4516825, $folderList->items[0]->accountId);
        self::assertEquals("Mon dossier", $folderList->items[0]->name);
        self::assertEquals("", $folderList->items[0]->description);
        self::assertEquals(0, $folderList->items[0]->books);
        self::assertEquals(0, $folderList->items[0]->subscribers);
        self::assertEquals(new DateTime("2015-10-06 16:55:39"), $folderList->items[0]->creation);
        self::assertEquals(new DateTime("2015-10-06 16:55:39"), $folderList->items[0]->modification);
        self::assertEquals("http://www.calameo.com/subscriptions/4540112", $folderList->items[0]->publicUrl);
    }
}
