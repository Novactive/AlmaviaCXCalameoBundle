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
use AlmaviaCX\Calameo\API\Value\Folder;
use AlmaviaCX\Calameo\API\Value\Response\Response;

class FolderSerializeTest extends SerializerTest
{

    public function testFolderResponse(): void
    {
        /** @var Folder $folder */
        $folder = $this->testDeserializeResponse(
            '{
  "response": {
    "status": "ok",
    "version": 2,
    "requestid": "eK4YhB",
    "requests": 161,
    "content": {
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
  }
}',
            Response::TYPE_OK,
            Folder::class
        );

        self::assertEquals(4540112, $folder->id);
        self::assertEquals(4516825, $folder->accountId);
        self::assertEquals("Mon dossier", $folder->name);
        self::assertEquals("", $folder->description);
        self::assertEquals(0, $folder->books);
        self::assertEquals(0, $folder->subscribers);
        self::assertEquals(new DateTime("2015-10-06 16:55:39"), $folder->creation);
        self::assertEquals(new DateTime("2015-10-06 16:55:39"), $folder->modification);
        self::assertEquals("http://www.calameo.com/subscriptions/4540112", $folder->publicUrl);
    }
}
