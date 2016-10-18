import re

hw_regex = re.compile(r".*[Hh]ello ?[Ww]orld.*")
tv_regex = re.compile(r"\b(\w*[aeiou]{3}\w*)\b")
al_regex = re.compile(r"[A-Z]{2}\d{3,4}")

test1 = "well hello world from hyfan"
test2 = "htere is geios and el"
test3 = "AZ082"

print(hw_regex.match(test1))
print(tv_regex.match(test2))
print(al_regex.match(test3))
