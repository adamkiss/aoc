use std::collections::HashMap;
use std::time::Instant;

fn set_or_increase(map: &mut HashMap<i64, i64>, key: i64, value: i64) {
    *map.entry(key).or_insert(0) += value;
}

fn part_keys(input: &str, blinks: i64) -> i64 {
    let initial: Vec<i64> = input.split_whitespace()
        .map(|num| num.parse().unwrap())
        .collect();

    let mut n = HashMap::new();
    for v in initial {
        set_or_increase(&mut n, v, 1);
    }

    for _ in 0..blinks {
        let mut n2 = HashMap::new();
        for (v, c) in &n {
            if *v == 0 {
                set_or_increase(&mut n2, 1, *c);
            } else if v.to_string().len() % 2 == 0 {
                let v_str = v.to_string();
                let l = v_str.len() / 2;
                let first_half = v_str[0..l].parse::<i64>().unwrap();
                let second_half = v_str[l..].parse::<i64>().unwrap();
                set_or_increase(&mut n2, first_half, *c);
                set_or_increase(&mut n2, second_half, *c);
            } else {
                set_or_increase(&mut n2, v * 2024, *c);
            }
        }
        n = n2;
    }

    n.values().sum()
}

fn main() {
    let input = "6571 0 5851763 526746 23 69822 9 989";
    let start_time = Instant::now();
    println!("{}", part_keys(input, 75));
    println!("Elapsed time: {:?}", start_time.elapsed());
}
