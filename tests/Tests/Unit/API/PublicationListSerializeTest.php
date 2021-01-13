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
use AlmaviaCX\Calameo\API\Value\PublicationList;
use AlmaviaCX\Calameo\API\Value\Response\Response;

class PublicationListSerializeTest extends SerializerTest
{

    public function testPublicationListResponse(): void
    {
        /** @var PublicationList $publicationList */
        $publicationList = $this->testDeserializeResponse(
            '{
  "response": {
    "status": "ok",
    "version": 2,
    "requestid": "ll7eoz",
    "requests": 209,
    "content": {
      "total": 1,
      "start": 0,
      "step": 10,
      "items": [
        {
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
      ]
    }
  }
}',
            Response::TYPE_OK,
            PublicationList::class
        );
        self::assertEquals(1, $publicationList->total);
        self::assertEquals(0, $publicationList->start);
        self::assertEquals(10, $publicationList->step);

        self::assertEquals("0045168254f0132f6cf5f", $publicationList->items[0]->id);
        self::assertEquals(4540112, $publicationList->items[0]->folderId);
        self::assertEquals(4516825, $publicationList->items[0]->accountId);
        self::assertEquals("Guided Tour EN", $publicationList->items[0]->name);
        self::assertEquals("www.calameo.com CALAMÉO GUIDED TOUR ENGLISH", $publicationList->items[0]->description);
        //        self::assertEquals( "MISC", $publicationList->items[0]->category );
        //        self::assertEquals( "MISC", $publicationList->items[0]->format );
        //        self::assertEquals( "en", $publicationList->items[0]->dialect );
        self::assertEquals("DONE", $publicationList->items[0]->status);
        //        self::assertEquals( 1, $publicationList->items[0]->isPublished );
        self::assertEquals(1, $publicationList->items[0]->isPrivate);
        self::assertEquals("dXT0xqsM5qjx", $publicationList->items[0]->authId);
        self::assertEquals(1, $publicationList->items[0]->allowMini);
        self::assertEquals(new DateTime("2015-10-16"), $publicationList->items[0]->date);
        self::assertEquals(12, $publicationList->items[0]->pages);
        self::assertEquals(595, $publicationList->items[0]->width);
        self::assertEquals(841, $publicationList->items[0]->height);
        self::assertEquals(0, $publicationList->items[0]->views);
        self::assertEquals(0, $publicationList->items[0]->downloads);
        self::assertEquals(0, $publicationList->items[0]->comments);
        self::assertEquals(0, $publicationList->items[0]->favorites);
        self::assertEquals(
            "http://i.calameoassets.com/151016113106-69d5960ce53f69b75910e802001e6adf/large.jpg",
            $publicationList->items[0]->posterUrl
        );
        self::assertEquals(
            "http://i.calameoassets.com/151016113106-69d5960ce53f69b75910e802001e6adf/thumb.jpg",
            $publicationList->items[0]->pictureUrl
        );
        self::assertEquals(
            "http://i.calameoassets.com/151016113106-69d5960ce53f69b75910e802001e6adf/p1.jpg",
            $publicationList->items[0]->thumbUrl
        );
        self::assertEquals(
            "http://www.calameo.com/books/0045168254f0132f6cf5f?authid=dXT0xqsM5qjx",
            $publicationList->items[0]->publicUrl
        );
        self::assertEquals(
            "http://www.calameo.com/read/0045168254f0132f6cf5f?authid=dXT0xqsM5qjx",
            $publicationList->items[0]->viewUrl
        );
        self::assertEquals(new DateTime("2015-10-16 11:31:06"), $publicationList->items[0]->creation);
        self::assertEquals(new DateTime("2015-10-16 11:31:51"), $publicationList->items[0]->modification);
    }
}
