## 📋General
<div align="center">
<img src="https://github.com/ClickedTran/GiftCode/blob/Master/icon.jpg">

  <p>This is a Plugin that allows Admin to generate a giftcode for the server with optional time and rewards</p>
</div>

## 📖Feature
- This is a Plugin that allows Admin to generate a giftcode for the server with optional time and rewards
<br>

## 📚For Developer

- You can access to GiftCode by using
- You can usage to:
<details>
  <summary>Click To See</summary>
 
 >- Create New GiftCode:

 ```php
  GiftCode::getInstance()->createCode(string $name, int $day, int $hour, int $minute, int $second, string $command);
```

 >- Remove GiftCode:

 ```php
   GiftCode::getInstance()->removeCode(string $name);
```

</details>
<br>

## 💬Command
| **COMMAND** | **DESCRIPTION** | **ALIASES** |
| --- | --- | --- |
| `giftcode` | GiftCode Commands | `code` |

## 📝Permission

<details>
<summary>Click to see permission</summary>

- Use `giftcode.command` to open menu GiftCode
- Use `giftcode.command.create` to create new giftcode in data
- Use `giftcode.command.remove` to remove giftcode existsing to data
- Use `giftcode.command.list` to see all giftcode in data
- Use `giftcode.command.help` to see all GiftCode Command
- Use `giftcode.command.redemption` redeem code

</details>

## 🖼️IMAGES
<h4>OP</h4>
<div align="center">
<img src="https://github.com/Clickedtran/GiftCode/blob/Master/image/op.png" width="250px" height="auto">
  <br>
<img src="https://github.com/Clickedtran/GiftCode/blob/Master/image/menu_create.png" width="250px" height="auto">
</div>
<br>
  
<h4>NO OP</h4>
<div align="center">
<img src="https://github.com/Clickedtran/GiftCode/blob/Master/image/no_op.png" width="250px" height="auto">
  <br>
<img src="https://github.com/Clickedtran/GiftCode/blob/Master/image/use.png" width="250px" height="auto">
</div>
