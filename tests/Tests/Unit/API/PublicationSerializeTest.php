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
use AlmaviaCX\Calameo\API\Value\Publication;
use AlmaviaCX\Calameo\API\Value\Response\Response;

class PublicationSerializeTest extends SerializerTest
{

    public function testPublicationResponse(): void
    {
        /** @var Publication $publication */
        $publication = $this->testDeserializeResponse(
            '{
  "response": {
    "status": "ok",
    "version": 2,
    "requestid": "uTPuZE",
    "requests": 208,
    "content": {
      "ID": "0045168254f0132f6cf5f",
      "SubscriptionID": 4540112,
      "AccountID": 4516825,
      "Name": "Guided Tour EN",
      "Description": "www.calameo.com CALAMÉO GUIDED TOUR ENGLISH",
      "Category": "MISC",
      "Format": "MISC",
      "Dialect": "en",
      "Status": "DONE",
      "IsPublished": 1,
      "IsPrivate": 1,
      "AuthID": "dXT0xqsM5qjx",
      "AllowMini": 1,
      "Date": "2015-10-16",
      "Pages": 12,
      "Width": 595,
      "Height": 841,
      "Views": 0,
      "Downloads": 0,
      "Comments": 0,
      "Favorites": 0,
      "PosterUrl": "http://i.calameoassets.com/151016113106-69d5960ce53f69b75910e802001e6adf/large.jpg",
      "PictureUrl": "http://i.calameoassets.com/151016113106-69d5960ce53f69b75910e802001e6adf/thumb.jpg",
      "ThumbUrl": "http://i.calameoassets.com/151016113106-69d5960ce53f69b75910e802001e6adf/p1.jpg",
      "PublicUrl": "http://www.calameo.com/books/0045168254f0132f6cf5f?authid=dXT0xqsM5qjx",
      "ViewUrl": "http://www.calameo.com/read/0045168254f0132f6cf5f?authid=dXT0xqsM5qjx",
      "Creation": "2015-10-16 11:31:06",
      "Modification": "2015-10-16 11:31:51"
    }
  }
}
',
            Response::TYPE_OK,
            Publication::class
        );
        self::assertEquals("0045168254f0132f6cf5f", $publication->id);
        self::assertEquals(4540112, $publication->folderId);
        self::assertEquals(4516825, $publication->accountId);
        self::assertEquals("Guided Tour EN", $publication->name);
        self::assertEquals("www.calameo.com CALAMÉO GUIDED TOUR ENGLISH", $publication->description);
        //        self::assertEquals( "MISC", $publication->category );
        //        self::assertEquals( "MISC", $publication->format );
        //        self::assertEquals( "en", $publication->dialect );
        self::assertEquals("DONE", $publication->status);
        //        self::assertEquals( 1, $publication->isPublished );
        self::assertEquals(1, $publication->isPrivate);
        self::assertEquals("dXT0xqsM5qjx", $publication->authId);
        self::assertEquals(1, $publication->allowMini);
        self::assertEquals(new DateTime("2015-10-16"), $publication->date);
        self::assertEquals(12, $publication->pages);
        self::assertEquals(595, $publication->width);
        self::assertEquals(841, $publication->height);
        self::assertEquals(0, $publication->views);
        self::assertEquals(0, $publication->downloads);
        self::assertEquals(0, $publication->comments);
        self::assertEquals(0, $publication->favorites);
        self::assertEquals(
            "http://i.calameoassets.com/151016113106-69d5960ce53f69b75910e802001e6adf/large.jpg",
            $publication->posterUrl
        );
        self::assertEquals(
            "http://i.calameoassets.com/151016113106-69d5960ce53f69b75910e802001e6adf/thumb.jpg",
            $publication->pictureUrl
        );
        self::assertEquals(
            "http://i.calameoassets.com/151016113106-69d5960ce53f69b75910e802001e6adf/p1.jpg",
            $publication->thumbUrl
        );
        self::assertEquals(
            "http://www.calameo.com/books/0045168254f0132f6cf5f?authid=dXT0xqsM5qjx",
            $publication->publicUrl
        );
        self::assertEquals(
            "http://www.calameo.com/read/0045168254f0132f6cf5f?authid=dXT0xqsM5qjx",
            $publication->viewUrl
        );
        self::assertEquals(new DateTime("2015-10-16 11:31:06"), $publication->creation);
        self::assertEquals(new DateTime("2015-10-16 11:31:51"), $publication->modification);
    }
}
